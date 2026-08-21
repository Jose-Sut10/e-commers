<?php
namespace App\Controllers;
use Throwable;
use RuntimeException;
use Core\Controller;
use Core\Request;
use Core\Session;
use App\Models\ShippingMethod;

class ShippingMethodController extends Controller{
    public function index(): void{
        view(
            'shipping_methods/index',
            [
                'title' =>
                    'Métodos de envío',

                'methods' =>
                    ShippingMethod::allOrdered(),
            ]
        );
    }

    public function store(): void{
        $request = new Request();
        $input = $request->all();

        $name =
            trim(
                (string) (
                    $input['name']
                    ?? ''
                )
            );

        $description =
            trim(
                (string) (
                    $input['description']
                    ?? ''
                )
            );

        $price =
            trim(
                (string) (
                    $input['price']
                    ?? ''
                )
            );

        $sortOrder =
            filter_var(
                $input['sort_order'] ?? 0,
                FILTER_VALIDATE_INT,
                [
                    'options' => [
                        'min_range' => 0,
                    ],
                ]
            );

        if (
            $name === ''
            || $price === ''
            || !is_numeric($price)
            || (float) $price < 0
        ) {
            Session::flash(
                'warning',
                'Revisa el nombre y el costo del método de envío.'
            );

            Session::flash(
                'old',
                $input
            );

            redirect('envios');
        }

        try {

            $method =
                new ShippingMethod([
                    'name' => $name,
                    'description' =>
                        $description === ''
                            ? null
                            : $description,

                    'price' => (float) $price,

                    'sort_order' =>
                        $sortOrder === false
                            ? 0
                            : (int) $sortOrder,

                    'active' =>
                        isset($input['active'])
                            ? 1
                            : 0,
                ]);

            if (!$method->save()) {
                throw new RuntimeException(
                    'No fue posible guardar el método de envío.'
                );
            }

            Session::flash(
                'success',
                'El método de envío fue creado correctamente.'
            );

        } catch (Throwable $exception) {

            error_log(
                $exception->getMessage()
            );

            Session::flash(
                'warning',
                'No fue posible guardar el método de envío.'
            );
        }

        redirect('envios');
    }

    public function update(): void{
        $request = new Request();
        $input = $request->all();

        $id =
            filter_var(
                $input['id'] ?? null,
                FILTER_VALIDATE_INT,
                [
                    'options' => [
                        'min_range' => 1,
                    ],
                ]
            );

        if (!$id) {
            Session::flash(
                'warning',
                'El método de envío no es válido.'
            );

            redirect('envios');
        }

        $method =
            ShippingMethod::find(
                (int) $id
            );

        if (!$method) {
            Session::flash(
                'warning',
                'El método de envío no fue encontrado.'
            );

            redirect('envios');
        }

        $name =
            trim(
                (string) (
                    $input['name']
                    ?? ''
                )
            );

        $price =
            trim(
                (string) (
                    $input['price']
                    ?? ''
                )
            );

        if (
            $name === ''
            || !is_numeric($price)
            || (float) $price < 0
        ) {
            Session::flash(
                'warning',
                'Revisa los datos del método de envío.'
            );

            redirect('envios');
        }

        $method->name = $name;

        $method->description =
            trim(
                (string) (
                    $input['description']
                    ?? ''
                )
            ) ?: null;

        $method->price =(float) $price;
        $method->sort_order =
            max(
                0,
                (int) (
                    $input['sort_order']
                    ?? 0
                )
            );

        $method->active =
            isset($input['active'])
                ? 1
                : 0;

        try {

            if (!$method->save()) {
                throw new RuntimeException(
                    'No fue posible actualizar el método.'
                );
            }

            Session::flash(
                'success',
                'El método de envío fue actualizado.'
            );

        } catch (Throwable $exception) {
            error_log($exception->getMessage());

            Session::flash(
                'warning',
                'No fue posible actualizar el método de envío.'
            );
        }

        redirect('envios');
    }
}