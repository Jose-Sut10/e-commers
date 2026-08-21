<?php
namespace App\Services;
use RuntimeException;
use App\Models\ShippingMethod;

class ShippingService{
    public function resolve(
        int $shippingMethodId
    ): array {
        $method =
            ShippingMethod::findActive(
                $shippingMethodId
            );

        if (!$method) {
            throw new RuntimeException(
                'El método de envío seleccionado ya no está disponible.'
            );
        }

        return [
            'method' => $method,
            'id' => (int) $method->id,
            'name' => (string) $method->name,
            'price' =>
                round(
                    (float) $method->price,
                    2
                ),
        ];
    }
}