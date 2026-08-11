<?php
namespace App\Controllers;
use Throwable;
use Core\Controller;
use Core\Request;
use Core\Session;
use App\Models\Company;
use App\Services\CartService;
use App\Services\OrderService;

class CheckoutController extends Controller{
    public function index(): void{
        $cart =
            new CartService();

        $items =
            $cart->items();

        if (empty($items)) {
            Session::flash(
                'warning',
                'Tu carrito está vacío.'
            );

            redirect('carrito');
        }

        view('checkout/index', [
            'title' => 'Finalizar compra',

            'company' =>
                Company::first(),

            'items' =>
                $items,

            'subtotal' =>
                $cart->subtotal(),
        ]);
    }

    public function store(): void{
        $cart =
            new CartService();

        $items =
            $cart->items();

        if (empty($items)) {
            Session::flash(
                'warning',
                'Tu carrito está vacío.'
            );

            redirect('carrito');
        }

        $request =
            new Request();

        $input =
            $request->all();

        $result = validator(
            $input,
            [
                'name' =>'required|min:3|max:150',
                'phone' =>'required|digits:8',
                'email' =>'email|max:150',
                'address' =>'required|max:500',
                'notes' =>'max:1000',
            ]
        )->validate();

        if ($result->fails()) {

            Session::flash(
                'errors',
                $result->errors()
            );

            Session::flash('old', [
                'name' =>
                    $input['name'] ?? '',

                'phone' =>
                    $input['phone'] ?? '',

                'email' =>
                    $input['email'] ?? '',

                'address' =>
                    $input['address'] ?? '',

                'notes' =>
                    $input['notes'] ?? '',
            ]);

            redirect('checkout');
        }

        $email = trim(
            (string) (
                $input['email'] ?? ''
            )
        );

        $notes = trim(
            (string) (
                $input['notes'] ?? ''
            )
        );

        $customer = [
            'name' =>
                trim(
                    (string) $input['name']
                ),

            'phone' =>
                trim(
                    (string) $input['phone']
                ),

            'email' =>
                $email === ''
                    ? null
                    : mb_strtolower($email),

            'address' =>
                trim(
                    (string) $input['address']
                ),

            'notes' =>
                $notes === ''
                    ? null
                    : $notes,
        ];

        try {
            $order =
                (
                    new OrderService()
                )->create(
                    $customer,
                    $items
                );

            /*
             * Solo vaciamos el carrito cuando
             * todo el pedido terminó correctamente.
             */
            $cart->clear();

            Session::flash(
                'completed_order_number',
                (string) $order->number
            );

            redirect(
                'pedido-confirmado'
            );

        } catch (Throwable $exception) {

            error_log(
                $exception->getMessage()
            );

            Session::flash(
                'errors',
                [
                    'general' => [
                        $exception->getMessage(),
                    ],
                ]
            );

            Session::flash('old', [
                'name' =>
                    $input['name'] ?? '',

                'phone' =>
                    $input['phone'] ?? '',

                'email' =>
                    $input['email'] ?? '',

                'address' =>
                    $input['address'] ?? '',

                'notes' =>
                    $input['notes'] ?? '',
            ]);

            redirect('checkout');
        }
    }

    public function success(): void{
        $number =
            session(
                'completed_order_number'
            );

        if (!$number) {
            redirect('tienda');
        }

        view('checkout/success', [
            'title' =>
                'Pedido confirmado',

            'number' =>
                $number,
        ]);
    }
}