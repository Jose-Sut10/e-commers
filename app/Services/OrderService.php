<?php
namespace App\Services;
use Throwable;
use RuntimeException;
use Core\Database;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Core\Auth\Auth;

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
            /*
             * Volvemos a cargar todos los productos
             * desde MySQL y bloqueamos las filas.
             */
            $items = [];

            $subtotal = 0.0;

            foreach ($cartItems as $cartItem) {

                $cartProduct =
                    $cartItem['product'];

                $quantity =
                    (int) $cartItem['quantity'];

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
                        'La cantidad de un producto no es válida.'
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

                /*
                 * Siempre utilizamos el precio actual
                 * de la base de datos.
                 */
                $unitPrice =
                    (float) $product->price;

                $itemSubtotal =
                    $unitPrice * $quantity;

                $subtotal +=
                    $itemSubtotal;

                $items[] = [
                    'product' =>
                        $product,

                    'quantity' =>
                        $quantity,

                    'unit_price' =>
                        $unitPrice,

                    'subtotal' =>
                        $itemSubtotal,
                ];
            }

            $number =
                $this->generateNumber();

            $order = new Order([
                'number' =>
                    $number,

                'customer_name' =>
                    $customer['name'],

                'customer_phone' =>
                    $customer['phone'],

                'customer_email' =>
                    $customer['email'],

                'customer_address' =>
                    $customer['address'],

                'notes' =>
                    $customer['notes'],

                'subtotal' =>
                    $subtotal,

                /*
                 * Por ahora no manejamos envío
                 * ni descuentos.
                 */
                'total' =>
                    $subtotal,

                'status' =>
                    'pending',
            ]);

            if (!$order->save()) {
                throw new RuntimeException(
                    'No fue posible crear el pedido.'
                );
            }

            $inventory =
                new InventoryService();

            foreach ($items as $item) {

                $product =
                    $item['product'];

                $orderItem =
                    new OrderItem([
                        'order_id' =>
                            (int) $order->id,

                        'product_id' =>
                            (int) $product->id,

                        /*
                         * Copias históricas.
                         */
                        'product_name' =>
                            $product->name,

                        'sku' =>
                            $product->sku,

                        'unit_price' =>
                            $item['unit_price'],

                        'quantity' =>
                            $item['quantity'],

                        'subtotal' =>
                            $item['subtotal'],
                    ]);

                if (!$orderItem->save()) {
                    throw new RuntimeException(
                        'No fue posible guardar el detalle del pedido.'
                    );
                }

                /*
                 * Registramos la salida del inventario.
                 * user_id = null porque la compra
                 * fue realizada por un cliente.
                 */
                $inventory->remove(
                    $product,
                    $item['quantity'],
                    null,
                    "Pedido {$number}"
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

    //administración de pedidos
    public function changeStatus(
    int $orderId,
    string $newStatus
    ): Order {
        Database::beginTransaction();

        try {
            $order = Order::findForUpdate(
                $orderId
            );

            if (!$order) {
                throw new RuntimeException(
                    'El pedido no fue encontrado.'
                );
            }

            $allowed =
                $order->allowedTransitions();

            if (!in_array(
                $newStatus,
                $allowed,
                true
            )) {
                throw new RuntimeException(
                    'El cambio de estado solicitado no está permitido.'
                );
            }

            /*
            * Si se cancela, devolvemos todo
            * el inventario correspondiente.
            */
            if ($newStatus === 'cancelled') {
                $items =
                    OrderItem::forOrder(
                        (int) $order->id
                    );

                $inventory =
                    new InventoryService();

                foreach ($items as $item) {
                    $product =
                        Product::find(
                            (int) $item->product_id
                        );

                    if (!$product) {
                        throw new RuntimeException(
                            "No se encontró el producto {$item->product_name}."
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

            $order->status =
                $newStatus;

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
}