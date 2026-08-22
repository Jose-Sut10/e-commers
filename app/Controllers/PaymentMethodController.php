<?php
namespace App\Controllers;
use Throwable;
use RuntimeException;
use Core\Controller;
use Core\Request;
use Core\Session;
use App\Models\PaymentMethod;

class PaymentMethodController extends Controller{
    /*LISTADO*/

    public function index(): void{
        view(
            'payment_methods/index',
            [
                'title' => 'Métodos de pago',
                'methods' => PaymentMethod::allOrdered(),
            ]
        );
    }

    /*CREAR*/

    public function store(): void{
        $request = new Request();
        $input = $request->all();

        try {
            $data = $this->validateData($input);

            /*Verificar código duplicado.*/

            if (
                PaymentMethod::findByCode(
                    $data['code']
                )
            ) {
                throw new RuntimeException(
                    'Ya existe un método de pago con ese código.'
                );
            }

            $method =
                new PaymentMethod([
                    'name' => $data['name'],
                    'code' => $data['code'],
                    'type' => $data['type'],
                    'instructions' => $data['instructions'],
                    'sort_order' => $data['sort_order'],
                    'active' => $data['active'],
                ]);

            if (!$method->save()) {
                throw new RuntimeException(
                    'No fue posible guardar el método de pago.'
                );
            }

            Session::flash(
                'success',
                'El método de pago fue creado correctamente.'
            );

        } catch (RuntimeException $exception) {

            Session::flash(
                'warning',
                $exception->getMessage()
            );

            Session::flash(
                'old',
                $input
            );

        } catch (Throwable $exception) {

            error_log($exception->getMessage());

            Session::flash(
                'warning',
                'Ocurrió un error al crear el método de pago.'
            );
        }

        redirect(
            'metodos-pago'
        );
    }

    /*ACTUALIZAR*/

    public function update(): void{
        $request = new Request();
        $input = $request->all();

        $id =
            filter_var(
                $input['id']
                ?? null,
                FILTER_VALIDATE_INT,
                [
                    'options' => ['min_range' => 1,],
                ]
            );


        if (!$id) {

            Session::flash(
                'warning',
                'El método de pago indicado no es válido.'
            );

            redirect(
                'metodos-pago'
            );
        }

        $method =
            PaymentMethod::find(
                (int) $id
            );

        if (!$method) {
            Session::flash(
                'warning',
                'El método de pago no fue encontrado.'
            );

            redirect('metodos-pago');
        }

        try {
            $data =
                $this->validateData(
                    $input
                );

            /*
             * Verificar código duplicado,
             * ignorando el registro actual.
             */

            if (
                PaymentMethod::findByCodeExceptId(
                    $data['code'],
                    (int) $method->id
                )
            ) {
                throw new RuntimeException(
                    'Ya existe otro método de pago con ese código.'
                );
            }

            $method->name = $data['name'];
            $method->code = $data['code'];
            $method->type = $data['type'];
            $method->instructions = $data['instructions'];
            $method->sort_order = $data['sort_order'];
            $method->active = $data['active'];

            if (!$method->save()) {
                throw new RuntimeException(
                    'No fue posible actualizar el método de pago.'
                );
            }

            Session::flash(
                'success',
                'El método de pago fue actualizado correctamente.'
            );

        } catch (RuntimeException $exception) {

            Session::flash(
                'warning',
                $exception->getMessage()
            );

        } catch (Throwable $exception) {

            error_log($exception->getMessage());

            Session::flash(
                'warning',
                'Ocurrió un error al actualizar el método de pago.'
            );
        }
        redirect('metodos-pago');
    }

    /*VALIDACIÓN */

    private function validateData(
        array $input
    ): array {

        $name =
            trim(
                (string) (
                    $input['name']
                    ?? ''
                )
            );

        if ($name === '') {
            throw new RuntimeException(
                'El nombre del método de pago es obligatorio.'
            );
        }

        if (mb_strlen($name) > 100) {
            throw new RuntimeException(
                'El nombre no puede superar los 100 caracteres.'
            );
        }

        /*
         * Código interno.
         *
         * Ejemplos:
         *
         * cash
         * bank_transfer
         */

        $code =
            strtolower(
                trim(
                    (string) (
                        $input['code']
                        ?? ''
                    )
                )
            );

        if ($code === '') {
            throw new RuntimeException(
                'El código del método de pago es obligatorio.'
            );
        }

        if (
            !preg_match(
                '/^[a-z0-9_-]+$/',
                $code
            )
        ) {
            throw new RuntimeException(
                'El código solo puede contener letras minúsculas, números, guiones y guion bajo.'
            );
        }

        /*
         * Tipo.
         */

        $type =
            trim(
                (string) (
                    $input['type']
                    ?? ''
                )
            );

        $allowedTypes = [
            'cash',
            'bank_transfer',
            'other',
        ];

        if (
            !in_array(
                $type,
                $allowedTypes,
                true
            )
        ) {
            throw new RuntimeException(
                'El tipo de método de pago no es válido.'
            );
        }

        /*Instrucciones*/

        $instructions =
            trim(
                (string) (
                    $input['instructions']
                    ?? ''
                )
            );


        /*Orden.*/

        $sortOrderRaw =
            $input['sort_order']
            ?? 0;

        $sortOrder =
            filter_var(
                $sortOrderRaw,
                FILTER_VALIDATE_INT,
                [
                    'options' => [
                        'min_range' => 0,
                    ],
                ]
            );

        if ($sortOrder === false) {
            $sortOrder = 0;
        }

        return [
            'name' => $name,
            'code' => $code,
            'type' => $type,
            'instructions' =>
                $instructions === ''
                    ? null
                    : $instructions,

            'sort_order' => (int) $sortOrder,

            'active' =>
                isset($input['active'])
                    ? 1
                    : 0,
        ];
    }
}