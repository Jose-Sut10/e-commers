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

class OrderService
{
    /*
     * =====================================================
     * CREAR PEDIDO
     * =====================================================
     */

    public function create(
        array $customer,
        array $cartItems,
        ?string $couponCode = null,
        ?int $shippingMethodId = null,
        ?int $paymentMethodId = null
    ): Order {

        if (empty($cartItems)) {
            throw new RuntimeException(
                'El carrito está vacío.'
            );
        }

        Database::beginTransaction();

        try {

            /*
             * =================================================
             * CLIENTE
             * =================================================
             */

            $customerModel =
                (
                    new CustomerService()
                )->findOrCreate(
                    $customer
                );

            /*
             * =================================================
             * VALIDAR PRODUCTOS Y CALCULAR SUBTOTAL
             * =================================================
             */

            $items = [];
            $subtotal = 0.0;

            foreach ($cartItems as $cartItem) {
                $cartProduct = $cartItem['product'];

                $cartVariant =
                    $cartItem['variant']
                    ?? null;

                $quantity = (int) $cartItem['quantity'];

                /*
                 * Bloqueamos el producto durante
                 * la creación del pedido.
                 */

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

                /*
                 * =================================================
                 * PRODUCTO CON VARIANTE
                 * =================================================
                 */

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

                    /*
                     * Este método ya toma en cuenta
                     * promociones de la variante.
                     */

                    $unitPrice =
                        $variant->finalPrice(
                            $product
                        );

                    $sku = (string) $variant->sku;
                    $variantName = (string) $variant->name;

                /*
                 * =================================================
                 * PRODUCTO SIN VARIANTE
                 * =================================================
                 */

                } else {
                    /*
                     * Si el producto utiliza variantes,
                     * obligamos a seleccionar una.
                     */

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

                    /*
                     * Este método ya toma en cuenta
                     * promociones del producto.
                     */

                    $unitPrice = $product->finalPrice();
                    $sku = (string) $product->sku;
                    $variantName = null;
                }

                /*
                 * =================================================
                 * SUBTOTAL DEL ARTÍCULO
                 * =================================================
                 */

                $itemSubtotal =
                    round(
                        $unitPrice
                        * $quantity,
                        2
                    );

                $subtotal += $itemSubtotal;

                $items[] = [

                    'product' => $product,
                    'variant' => $variant,
                    'variant_name' => $variantName,
                    'sku' => $sku,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $itemSubtotal,
                ];
            }

            /** Redondeamos el subtotal final.*/

            $subtotal =
                round(
                    $subtotal,
                    2
                );

            /*
             * =================================================
             * CUPÓN
             * =================================================
             *
             * Aquí estaba la parte que faltaba
             * en tu archivo.
             */

            $couponResult =
                (
                    new CouponService()
                )->consume(
                    $couponCode,
                    $subtotal
                );

            $coupon = $couponResult['coupon'];
            $discount = (float)$couponResult['discount'];

            /*
             * =================================================
             * MÉTODO DE ENVÍO
             * =================================================
             */

            if (!$shippingMethodId) {
                throw new RuntimeException(
                    'Debes seleccionar un método de envío.'
                );
            }

            $shippingResult =
                (
                    new ShippingService()
                )->resolve(
                    $shippingMethodId
                );

            $shipping = $shippingResult['method'];
            $shippingTotal = (float) $shippingResult['price'];

            /*MÉTODO DE PAGO*/

            if (!$paymentMethodId) {
                throw new RuntimeException(
                    'Debes seleccionar un método de pago.'
                );
            }

            $paymentResult =
                (
                    new PaymentService()
                )->resolve(
                    $paymentMethodId
                );

            $paymentMethod =
                $paymentResult['method'];

            /*TOTAL DEL PEDIDO
             * subtotal
             * - descuento
             * + envío
             */

            $total =
                round(
                    $subtotal
                    - $discount
                    + $shippingTotal,
                    2
                );

            /*NÚMERO DEL PEDIDO*/
            $number = $this->generateNumber();

            /*CREAR PEDIDO*/

            $order =
                new Order([

                    /*
                     * Cliente
                     */

                    'customer_id' => (int) $customerModel->id,

                    /*
                     * Cupón
                     */

                    'coupon_id' =>
                        $coupon
                            ? (int) $coupon->id
                            : null,

                    /*
                     * Envío
                     */

                    'shipping_method_id' => (int) $shipping->id,
                    'payment_method_id' => (int) $paymentMethod->id,

                    /*
                     * Pedido
                     */

                    'number' => $number,

                    /*
                     * Datos del cliente
                     */

                    'customer_name' => $customer['name'],
                    'customer_phone' => $customer['phone'],
                    'customer_email' => $customer['email'],
                    'customer_address' => $customer['address'],
                    'notes' => $customer['notes'],

                    /*
                     * Guardamos también el código
                     * como dato histórico.
                     */

                    'coupon_code' =>
                        $coupon
                            ? (string) $coupon->code
                            : null,

                    /*
                     * Guardamos el nombre del envío
                     * como dato histórico.
                     */

                    'shipping_method_name' => (string) $shipping->name,
                    'payment_method_name' => (string) $paymentMethod->name,
                    'payment_method_code' => (string) $paymentMethod->code,
                    /*
                     * Totales
                     */

                    'subtotal' => $subtotal,
                    'discount_total' =>  $discount,
                    'shipping_total' => $shippingTotal,
                    'payment_status' => 'pending',
                    'paid_at' => null,
                    'total' => $total,

                    /*
                     * Estado inicial
                     */
                    'status' => 'pending',
                ]);

            if (!$order->save()) {
                throw new RuntimeException(
                    'No fue posible crear el pedido.'
                );
            }

            /*
             * =================================================
             * INVENTARIO
             * =================================================
             */

            $inventory = new InventoryService();

            foreach ($items as $item) {
                $product = $item['product'];
                $variant = $item['variant'];

                /*
                 * =================================================
                 * GUARDAR DETALLE DEL PEDIDO
                 * =================================================
                 */

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

                /*
                 * =================================================
                 * DESCONTAR INVENTARIO
                 * =================================================
                 */

                if ($variant) {
                    $inventory->removeVariant(
                        $variant,
                        (int) $item['quantity'],
                        null,
                        "Pedido {$number}"
                    );

                } else {
                    $inventory->remove(
                        $product,
                        (int) $item['quantity'],
                        null,
                        "Pedido {$number}"
                    );
                }
            }

            /*
             * Todo salió correctamente.
             */

            Database::commit();
            return $order;

        } catch (Throwable $exception) {

            /*
             * Si falla:
             *
             * - pedido
             * - cupón
             * - inventario
             * - cliente
             * - envío
             *
             * revertimos todo.
             */

            if (
                Database::inTransaction()
            ) {
                Database::rollBack();
            }
            throw $exception;
        }
    }

    /*
     * =====================================================
     * CAMBIAR ESTADO DEL PEDIDO
     * =====================================================
     */

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

            /*
             * Validar transición.
             */

            if (
                !in_array(
                    $newStatus,
                    $order->allowedTransitions(),
                    true
                )
            ) {
                throw new RuntimeException(
                    'El cambio de estado solicitado no está permitido.'
                );
            }

            /*
             * =================================================
             * CANCELACIÓN
             * =================================================
             *
             * Si cancelamos un pedido,
             * devolvemos las existencias.
             */

            if ($newStatus === 'cancelled') {

                $items =
                    OrderItem::forOrder(
                        (int) $order->id
                    );

                $inventory = new InventoryService();

                foreach ($items as $item) {

                    /*
                     * Variante
                     */

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

                    /*
                     * Producto base
                     */

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

            /*
             * Actualizar estado.
             */
            $order->status = $newStatus;

            if (!$order->save()) {
                throw new RuntimeException(
                    'No fue posible actualizar el estado del pedido.'
                );
            }

            Database::commit();
            return $order;

        } catch (Throwable $exception) {

            if (
                Database::inTransaction()
            ) {
                Database::rollBack();
            }

            throw $exception;
        }
    }

    /*
     * =====================================================
     * GENERAR NÚMERO DE PEDIDO
     * =====================================================
     */

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