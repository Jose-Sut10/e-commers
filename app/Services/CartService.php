<?php
namespace App\Services;
use RuntimeException;
use Core\Session;
use App\Models\Product;

class CartService{
    private const SESSION_KEY = 'cart';

    public function raw(): array{
        $cart = Session::get(
            self::SESSION_KEY,
            []
        );

        return is_array($cart)
            ? $cart
            : [];
    }

    public function add(
        Product $product,
        int $quantity = 1
    ): void {
        if ($quantity < 1) {
            throw new RuntimeException(
                'La cantidad debe ser mayor que cero.'
            );
        }

        $stock = (int) $product->stock;

        if ($stock <= 0) {
            throw new RuntimeException(
                'Este producto está agotado.'
            );
        }

        $cart = $this->raw();

        $productId = (int) $product->id;

        $currentQuantity =
            (int) ($cart[$productId] ?? 0);

        $newQuantity =
            $currentQuantity + $quantity;

        if ($newQuantity > $stock) {
            throw new RuntimeException(
                'No hay suficientes existencias disponibles.'
            );
        }

        $cart[$productId] =
            $newQuantity;

        Session::put(
            self::SESSION_KEY,
            $cart
        );
    }

    public function update(
        int $productId,
        int $quantity
    ): void {
        $cart = $this->raw();

        if (!isset($cart[$productId])) {
            throw new RuntimeException(
                'El producto no está en el carrito.'
            );
        }

        if ($quantity <= 0) {
            $this->remove($productId);

            return;
        }

        $product =
            Product::findPublicById(
                $productId
            );

        if (!$product) {
            $this->remove($productId);

            throw new RuntimeException(
                'El producto ya no está disponible.'
            );
        }

        if ($quantity > (int) $product->stock) {
            throw new RuntimeException(
                'La cantidad solicitada supera las existencias disponibles.'
            );
        }

        $cart[$productId] =
            $quantity;

        Session::put(
            self::SESSION_KEY,
            $cart
        );
    }

    public function remove(
        int $productId
    ): void {
        $cart = $this->raw();

        unset(
            $cart[$productId]
        );

        Session::put(
            self::SESSION_KEY,
            $cart
        );
    }

    public function clear(): void{
        Session::forget(
            self::SESSION_KEY
        );
    }

    public function items(): array{
        $cart = $this->raw();

        $items = [];

        foreach ($cart as $productId => $quantity) {
            $product =
                Product::findPublicById(
                    (int) $productId
                );

            /*
             * Si el producto fue eliminado o desactivado,
             * dejamos de mostrarlo en el carrito.
             */
            if (!$product) {
                $this->remove(
                    (int) $productId
                );

                continue;
            }

            $quantity =
                (int) $quantity;

            /*
             * Si el stock cambió desde que se agregó,
             * ajustamos la cantidad al máximo disponible.
             */
            if ($quantity > (int) $product->stock) {
                $quantity =
                    (int) $product->stock;

                if ($quantity <= 0) {
                    $this->remove(
                        (int) $productId
                    );

                    continue;
                }

                $this->update(
                    (int) $productId,
                    $quantity
                );
            }

            $items[] = [
                'product' =>
                    $product,

                'quantity' =>
                    $quantity,

                'subtotal' =>
                    (float) $product->price
                    * $quantity,
            ];
        }

        return $items;
    }

    public function count(): int{
        return array_sum(
            array_map(
                'intval',
                $this->raw()
            )
        );
    }

    public function subtotal(): float{
        $subtotal = 0;

        foreach ($this->items() as $item) {
            $subtotal +=
                (float) $item['subtotal'];
        }

        return $subtotal;
    }
}