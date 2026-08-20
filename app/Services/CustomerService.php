<?php
namespace App\Services;
use RuntimeException;
use App\Models\Customer;

class CustomerService{
    public function findOrCreate(
        array $data
    ): Customer {
        $phone = trim(
            (string) (
                $data['phone']
                ?? ''
            )
        );

        if ($phone === '') {
            throw new RuntimeException(
                'El teléfono del cliente es obligatorio.'
            );
        }

        $customer =
            Customer::findByPhone(
                $phone
            );

        /*
         * Si ya existe, actualizamos sus
         * datos con la información más
         * reciente del checkout.
         */
        if ($customer) {
            $customer->name =
                trim(
                    (string) (
                        $data['name']
                        ?? $customer->name
                    )
                );

            $customer->email =
                $this->nullable(
                    $data['email']
                    ?? null
                );

            $customer->address =
                $this->nullable(
                    $data['address']
                    ?? null
                );

            $customer->active = 1;

            if (!$customer->save()) {
                throw new RuntimeException(
                    'No fue posible actualizar los datos del cliente.'
                );
            }

            return $customer;
        }

        /*
         * Cliente nuevo.
         */

        $customer =
            new Customer([
                'name' =>
                    trim(
                        (string) (
                            $data['name']
                            ?? ''
                        )
                    ),

                'phone' =>
                    $phone,

                'email' =>
                    $this->nullable(
                        $data['email']
                        ?? null
                    ),

                'address' =>
                    $this->nullable(
                        $data['address']
                        ?? null
                    ),

                'active' =>
                    1,
            ]);

        if (!$customer->save()) {
            throw new RuntimeException(
                'No fue posible registrar al cliente.'
            );
        }
        return $customer;
    }

    private function nullable(
        mixed $value
    ): ?string {
        $value =
            trim(
                (string) $value
            );

        return $value === ''
            ? null
            : $value;
    }
}