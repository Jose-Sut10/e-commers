<?php
namespace App\Controllers;
use Core\Controller;
use Core\Request;
use Core\Session;

use App\Models\Company;
use App\Models\Order;

class TrackingController extends Controller{
    /*FORMULARIO*/

    public function index(): void{
        view(
            'tracking/index',
            [
                'title' => 'Seguimiento de pedido',
                'company' => Company::first(),
                'order' => null,
                'searched' => false,
            ],
            'shop'
        );
    }

    /*BUSCAR PEDIDO*/

    public function search(): void{
        $request = new Request();
        $input = $request->all();
        $number =
            strtoupper(
                trim(
                    (string) (
                        $input['number']
                        ?? ''
                    )
                )
            );

        $phone =
            trim(
                (string) (
                    $input['phone']
                    ?? ''
                )
            );

        /*Validación del número.*/

        if ($number === '') {
            Session::flash(
                'tracking_error',
                'Escribe el número de tu pedido.'
            );

            Session::flash(
                'tracking_old',
                [
                    'number' => $number,
                    'phone' => $phone,
                ]
            );
            redirect('seguimiento');
        }

        /*
         * Teléfono de Guatemala:
         * exactamente 8 dígitos.
         */

        if (
            !preg_match(
                '/^\d{8}$/',
                $phone
            )
        ) {

            Session::flash(
                'tracking_error',
                'El teléfono debe contener exactamente 8 dígitos.'
            );

            Session::flash(
                'tracking_old',
                [
                    'number' => $number,
                    'phone' => $phone,
                ]
            );

            redirect('seguimiento');
        }

        /*
         * Buscar usando ambos datos.
         *
         * Esto evita mostrar un pedido
         * solamente por conocer su número.
         */

        $order =
            Order::findForTracking(
                $number,
                $phone
            );

        view(
            'tracking/index',
            [
                'title' => 'Seguimiento de pedido',
                'company' => Company::first(),
                'order' => $order,
                'searched' => true,
                'number' => $number,
                'phone' => $phone,
            ],
            'shop'
        );
    }
}