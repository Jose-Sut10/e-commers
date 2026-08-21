<?php
namespace App\Services;
use DateTime;
use RuntimeException;
use App\Models\Product;
use App\Models\ProductVariant;

class PromotionService{
    public function updateProduct(
        Product $product,
        array $data
    ): void {
        $promotion =
            $this->validatePromotion(
                $data,
                (float) $product->price
            );

        $product->sale_price = $promotion['sale_price'];
        $product->sale_starts_at = $promotion['starts_at'];
        $product->sale_ends_at = $promotion['ends_at'];

        if (!$product->save()) {
            throw new RuntimeException(
                'No fue posible guardar la promoción.'
            );
        }
    }

    public function updateVariant(
        ProductVariant $variant,
        Product $product,
        array $data
    ): void {
        $basePrice =
            $variant->basePrice(
                $product
            );

        $promotion =
            $this->validatePromotion(
                $data,
                $basePrice
            );

        $variant->sale_price = $promotion['sale_price'];
        $variant->sale_starts_at = $promotion['starts_at'];
        $variant->sale_ends_at = $promotion['ends_at'];

        if (!$variant->save()) {
            throw new RuntimeException(
                'No fue posible guardar la promoción de la variante.'
            );
        }
    }

    private function validatePromotion(
        array $data,
        float $regularPrice
    ): array {
        $salePriceValue =
            trim(
                (string) (
                    $data['sale_price']
                    ?? ''
                )
            );

        /*
         * Si el precio de oferta queda
         * vacío, eliminamos la promoción.
         */

        if ($salePriceValue === '') {
            return [
                'sale_price' => null,
                'starts_at' => null,
                'ends_at' => null,
            ];
        }

        if (!is_numeric($salePriceValue)) {
            throw new RuntimeException(
                'El precio de oferta debe ser numérico.'
            );
        }

        $salePrice = (float) $salePriceValue;

        if ($salePrice < 0) {
            throw new RuntimeException(
                'El precio de oferta no puede ser negativo.'
            );
        }

        if ($salePrice >= $regularPrice) {
            throw new RuntimeException(
                'El precio de oferta debe ser menor que el precio normal.'
            );
        }

        $startsAt =
            $this->parseDate(
                $data['sale_starts_at']
                ?? null
            );

        $endsAt =
            $this->parseDate(
                $data['sale_ends_at']
                ?? null
            );

        if (
            $startsAt !== null
            && $endsAt !== null
            && strtotime($endsAt)
                <= strtotime($startsAt)
        ) {
            throw new RuntimeException(
                'La fecha de finalización debe ser posterior a la fecha de inicio.'
            );
        }

        return [
            'sale_price' => $salePrice,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ];
    }

    private function parseDate(
        mixed $value
    ): ?string {
        $value =
            trim(
                (string) $value
            );

        if ($value === '') {
            return null;
        }

        $date =
            DateTime::createFromFormat(
                'Y-m-d\TH:i',
                $value
            );

        if (!$date) {
            throw new RuntimeException(
                'Una de las fechas de promoción no es válida.'
            );
        }

        $errors = DateTime::getLastErrors();

        if (
            $errors !== false
            && (
                $errors['warning_count'] > 0
                || $errors['error_count'] > 0
            )
        ) {
            throw new RuntimeException(
                'Una de las fechas de promoción no es válida.'
            );
        }

        return $date->format(
            'Y-m-d H:i:s'
        );
    }
}