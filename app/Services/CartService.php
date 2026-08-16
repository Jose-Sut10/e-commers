<?php
namespace App\Services;
use RuntimeException;
use Core\Session;
use App\Models\Product;
use App\Models\ProductVariant;

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
        int $quantity = 1,
        ?ProductVariant $variant = null
    ): void {
        if ($quantity < 1) {
            throw new RuntimeException(
                'La cantidad debe ser mayor que cero.'
            );
        }

        $variants =
            ProductVariant::forProduct(
                (int) $product->id,
                true
            );

        $hasVariants =
            !empty($variants);

        if ($hasVariants && !$variant) {
            throw new RuntimeException(
                'Debes seleccionar una variante.'
            );
        }

        if ($variant) {
            if (
                (int) $variant->product_id
                !== (int) $product->id
            ) {
                throw new RuntimeException(
                    'La variante seleccionada no pertenece al producto.'
                );
            }
            $stock =(int) $variant->stock;
            $variantId =(int) $variant->id;
        } else {
            $stock =(int) $product->stock;
            $variantId = null;
        }


        if ($stock <= 0) {
            throw new RuntimeException(
                'Este producto está agotado.'
            );
        }


        $key = $this->key(
            (int) $product->id,
            $variantId
        );

        $cart =$this->raw();

        $current =
            (int) (
                $cart[$key]['quantity']
                ?? 0
            );

        $newQuantity =$current + $quantity;

        if ($newQuantity > $stock) {
            throw new RuntimeException(
                'No hay suficientes existencias disponibles.'
            );
        }


        $cart[$key] = [
            'product_id' =>
                (int) $product->id,

            'variant_id' =>
                $variantId,

            'quantity' =>
                $newQuantity,
        ];


        Session::put(
            self::SESSION_KEY,
            $cart
        );
    }


    public function update(
        int $productId,
        ?int $variantId,
        int $quantity
    ): void {
        $key =
            $this->key(
                $productId,
                $variantId
            );

        $cart =
            $this->raw();


        if (!isset($cart[$key])) {
            throw new RuntimeException(
                'El producto no está en el carrito.'
            );
        }


        if ($quantity <= 0) {
            $this->remove(
                $productId,
                $variantId
            );

            return;
        }

        $product =
            Product::findPublicById(
                $productId
            );

        if (!$product) {
            $this->remove(
                $productId,
                $variantId
            );

            throw new RuntimeException(
                'El producto ya no está disponible.'
            );
        }

        if ($variantId !== null) {

            $variant =
                ProductVariant::findPublicForProduct(
                    $variantId,
                    $productId
                );

            if (!$variant) {
                $this->remove(
                    $productId,
                    $variantId
                );

                throw new RuntimeException(
                    'La variante ya no está disponible.'
                );
            }

            $stock =(int) $variant->stock;

        } else {

            $stock =(int) $product->stock;
        }


        if ($quantity > $stock) {
            throw new RuntimeException(
                'La cantidad solicitada supera las existencias disponibles.'
            );
        }

        $cart[$key]['quantity'] =
            $quantity;

        Session::put(
            self::SESSION_KEY,
            $cart
        );
    }

    public function remove(
        int $productId,
        ?int $variantId = null
    ): void {
        $cart =
            $this->raw();

        unset(
            $cart[
                $this->key(
                    $productId,
                    $variantId
                )
            ]
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
        $cart =
            $this->raw();

        $items = [];

        foreach ($cart as $key => $entry) {

            if (!is_array($entry)) {
                unset($cart[$key]);
                continue;
            }

            $productId =
                (int) (
                    $entry['product_id']
                    ?? 0
                );

            $variantId =
                isset($entry['variant_id'])
                && $entry['variant_id'] !== null
                    ? (int) $entry['variant_id']
                    : null;

            $quantity =
                (int) (
                    $entry['quantity']
                    ?? 0
                );

            $product =
                Product::findPublicById(
                    $productId
                );

            if (
                !$product
                || $quantity <= 0
            ) {
                $this->remove(
                    $productId,
                    $variantId
                );

                continue;
            }

            $variant = null;

            if ($variantId !== null) {
                $variant =
                    ProductVariant::findPublicForProduct(
                        $variantId,
                        $productId
                    );

                if (!$variant) {
                    $this->remove(
                        $productId,
                        $variantId
                    );

                    continue;
                }

                $stock =(int) $variant->stock;

                $unitPrice =
                    $variant->finalPrice(
                        $product
                    );

            } else {
                $stock =(int) $product->stock;

                $unitPrice =(float) $product->price;
            }

            if ($stock <= 0) {
                $this->remove(
                    $productId,
                    $variantId
                );

                continue;
            }

            if ($quantity > $stock) {
                $quantity = $stock;

                $this->update(
                    $productId,
                    $variantId,
                    $quantity
                );
            }

            $items[] = [
                'product' =>$product,
                'variant' =>$variant,
                'quantity' =>$quantity,
                'unit_price' =>$unitPrice,
                'subtotal' =>$unitPrice * $quantity,
            ];
        }
        return $items;
    }

    public function count(): int{
        $count = 0;

        foreach ($this->raw() as $entry) {
            if (is_array($entry)) {
                $count +=
                    (int) (
                        $entry['quantity']
                        ?? 0
                    );
            }
        }
        return $count;
    }

    public function subtotal(): float{
        $subtotal = 0;

        foreach ($this->items() as $item) {
            $subtotal +=
                (float) $item['subtotal'];
        }

        return $subtotal;
    }

    private function key(
        int $productId,
        ?int $variantId
    ): string {
        return sprintf(
            '%d:%d',
            $productId,
            $variantId ?? 0
        );
    }
}