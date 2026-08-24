<?php
namespace App\Services;
use RuntimeException;
use App\Models\PaymentMethod;

class PaymentService{
    public function resolve(
        int $paymentMethodId
    ): array {

        $method =
            PaymentMethod::findActive(
                $paymentMethodId
            );

        if (!$method) {
            throw new RuntimeException(
                'El método de pago seleccionado ya no está disponible.'
            );
        }

        return [
            'method' => $method,
            'id' => (int) $method->id,
            'name' => (string) $method->name,
            'code' => (string) $method->code,
            'type' => (string) $method->type,
            'instructions' =>
                $method->instructions
                    ? (string) $method->instructions
                    : null,
        ];
    }
}