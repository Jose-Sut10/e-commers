<?php
namespace App\Services;
use Throwable;
use RuntimeException;
use Core\Auth\Auth;
use Core\Database;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;

class OrderService{
    public function create(
        array $customer,
        array $cartItems
    ): Order {
        if (empty($cartItems)) {
            throw new RuntimeException(
                'El carrito está vacío.'
            );
        }

        Database::beginTransaction();

        try {

                $customerModel =
            (
                new CustomerService()
            )->findOrCreate(
                $customer
            );

            $items = [];
            $subtotal = 0.0;

            foreach ($cartItems as $cartItem) {

                $cartProduct =$cartItem['product'];

                $cartVariant =
                    $cartItem['variant']
                    ?? null;

                $quantity =(int) $cartItem['quantity'];

                $product =
                    Product::findPublicForUpdate(
                        (int) $cartProduct->id
                    );

                if (!$product) {
                    throw new RuntimeException(
                        'Uno de los productos ya no está disponible.'
                    );
                }

                if ($quantity < 1) {
                    throw new RuntimeException(
                        'La cantidad solicitada no es válida.'
                    );
                }

                $variant = null;

                if ($cartVariant) {

                    $variant =
                        ProductVariant::findPublicForUpdate(
                            (int) $cartVariant->id,
                            (int) $product->id
                        );

                    if (!$variant) {
                        throw new RuntimeException(
                            "La variante seleccionada de {$product->name} ya no está disponible."
                        );
                    }

                    if (
                        $quantity
                        > (int) $variant->stock
                    ) {
                        throw new RuntimeException(
                            "No hay suficientes existencias de {$product->name} - {$variant->name}."
                        );
                    }


                    $unitPrice =
                        $variant->finalPrice(
                            $product
                        );

                    $sku =(string) $variant->sku;
                    $variantName =(string) $variant->name;

                } else {

                    if (
                        ProductVariant::existsForProduct(
                            (int) $product->id
                        )
                    ) {
                        throw new RuntimeException(
                            "Debes seleccionar una variante de {$product->name}."
                        );
                    }

                    if (
                        $quantity
                        > (int) $product->stock
                    ) {
                        throw new RuntimeException(
                            "No hay suficientes existencias de {$product->name}."
                        );
                    }

                    $unitPrice =(float) $product->price;
                    $sku =(string) $product->sku;
                    $variantName =null;
                }


                $itemSubtotal = $unitPrice * $quantity;
                $subtotal += $itemSubtotal;

                $items[] = [
                    'product' => $product,
                    'variant' => $variant,
                    'variant_name' => $variantName,
                    'sku' => $sku,
                    'quantity' => $quantity,
                    'unit_price' =>  $unitPrice,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $number = $this->generateNumber();

            $order = new Order([
                'customer_id' => (int) $customerModel->id,
                'number' => $number,
                'customer_name' => $customer['name'],
                'customer_phone' => $customer['phone'],
                'customer_email' => $customer['email'],
                'customer_address' => $customer['address'],
                'notes' => $customer['notes'],
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'status' =>'pending',
            ]);

            if (!$order->save()) {
                throw new RuntimeException(
                    'No fue posible crear el pedido.'
                );
            }

            $inventory =
                new InventoryService();

            foreach ($items as $item) {
                $product = $item['product'];
                $variant = $item['variant'];

                $orderItem =
                    new OrderItem([
                        'order_id' => (int) $order->id,
                        'product_id' => (int) $product->id,
                        'variant_id' =>
                            $variant
                                ? (int) $variant->id
                                : null,

                        'product_name' => (string) $product->name,
                        'variant_name' => $item['variant_name'],
                        'sku' => $item['sku'],
                        'unit_price' => $item['unit_price'],
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['subtotal'],
                    ]);

                if (!$orderItem->save()) {
                    throw new RuntimeException(
                        'No fue posible guardar el detalle del pedido.'
                    );
                }

                if ($variant) {
                    $inventory->removeVariant(
                        $variant,
                        $item['quantity'],
                        null,
                        "Pedido {$number}"
                    );

                } else {

                    $inventory->remove(
                        $product,
                        $item['quantity'],
                        null,
                        "Pedido {$number}"
                    );
                }
            }
            Database::commit();
            return $order;
        } catch (Throwable $exception) {

            if (Database::inTransaction()) {
                Database::rollBack();
            }
            throw $exception;
        }
    }

    public function changeStatus(
        int $orderId,
        string $newStatus
    ): Order {
        Database::beginTransaction();

        try {

            $order =
                Order::findForUpdate(
                    $orderId
                );

            if (!$order) {
                throw new RuntimeException(
                    'El pedido no fue encontrado.'
                );
            }

            if (!in_array(
                $newStatus,
                $order->allowedTransitions(),
                true
            )) {
                throw new RuntimeException(
                    'El cambio de estado solicitado no está permitido.'
                );
            }

            if ($newStatus === 'cancelled') {

                $items =
                    OrderItem::forOrder(
                        (int) $order->id
                    );

                $inventory = new InventoryService();

                foreach ($items as $item) {

                    if ($item->variant_id) {

                        $variant =
                            ProductVariant::find(
                                (int) $item->variant_id
                            );

                        if (!$variant) {
                            throw new RuntimeException(
                                "No fue posible encontrar la variante {$item->variant_name}."
                            );
                        }

                        $inventory->addVariant(
                            $variant,
                            (int) $item->quantity,
                            Auth::id(),
                            "Cancelación del pedido {$order->number}"
                        );

                    } else {

                        $product =
                            Product::find(
                                (int) $item->product_id
                            );


                        if (!$product) {
                            throw new RuntimeException(
                                "No fue posible encontrar el producto {$item->product_name}."
                            );
                        }

                        $inventory->add(
                            $product,
                            (int) $item->quantity,
                            Auth::id(),
                            "Cancelación del pedido {$order->number}"
                        );
                    }
                }
            }
            $order->status = $newStatus;

            if (!$order->save()) {
                throw new RuntimeException(
                    'No fue posible actualizar el estado del pedido.'
                );
            }
            Database::commit();
            return $order;

        } catch (Throwable $exception) {

            if (Database::inTransaction()) {
                Database::rollBack();
            }
            throw $exception;
        }
    }

    private function generateNumber(): string{
        return sprintf(
            'ORD-%s-%s',
            date('Ymd'),
            strtoupper(
                bin2hex(
                    random_bytes(3)
                )
            )
        );
    }
}