<?php
namespace App\Controllers;
use Throwable;
use RuntimeException;
use Core\Controller;
use Core\Request;
use Core\Session;
use App\Models\Company;
use App\Models\ShippingMethod;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\CouponService;


class CheckoutController extends Controller{
    public function index(): void{
        $cart = new CartService();
        $items = $cart->items();

        if (empty($items)) {

            Session::flash(
                'warning',
                'Tu carrito está vacío.'
            );

            redirect('carrito');
        }

        $subtotal = $cart->subtotal();

        $couponCode =
            (string) Session::get(
                'checkout_coupon_code',
                ''
            );

        $couponResult = [
            'coupon' => null,
            'code' => null,
            'discount' => 0.0,
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ];

        if ($couponCode !== '') {

            try {
                $couponResult =
                    (
                        new CouponService()
                    )->preview(
                        $couponCode,
                        $subtotal
                    );

            } catch (RuntimeException $exception) {

                Session::forget(
                    'checkout_coupon_code'
                );

                Session::flash(
                    'warning',
                    $exception->getMessage()
                );

                redirect('checkout');
            }
        }

        $shippingMethods = ShippingMethod::activeOrdered();

        view(
            'checkout/index',
            [
                'title' => 'Finalizar compra',
                'company' => Company::first(),
                'items' => $items,
                'subtotal' => $subtotal,
                'couponResult' => $couponResult,
                'shippingMethods' =>$shippingMethods,
            ],
            'shop'
        );
    }

    public function store(): void{
        $cart = new CartService();
        $items = $cart->items();

        if (empty($items)) {
            Session::flash(
                'warning',
                'Tu carrito está vacío.'
            );
            redirect('carrito');
        }

        $request = new Request();
        $input = $request->all();

        $result =
            validator(
                $input,
                [
                    'name' => 'required|min:3|max:150',
                    'phone' => 'required|digits:8',
                    'email' => 'email|max:150',
                    'address' => 'required|max:500',
                    'notes' => 'max:1000',
                    'shipping_method_id' => 'required|numeric|min:1',
                ]
            )->validate();

        $shippingMethodId =
            filter_var(
                $input['shipping_method_id']
                ?? null,
                FILTER_VALIDATE_INT,
                [
                    'options' => [
                        'min_range' => 1,
                    ],
                ]
            );

        if (!$shippingMethodId) {
            $result->add(
                'shipping_method_id',
                'Selecciona un método de envío.'
            );
        }

        if ($result->fails()) {
            Session::flash(
                'errors',
                $result->errors()
            );
            $this->saveOldInput(
                $input
            );
            redirect('checkout');
        }

        $email =
            trim(
                (string) (
                    $input['email']
                    ?? ''
                )
            );

        $notes =
            trim(
                (string) (
                    $input['notes']
                    ?? ''
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

        $couponCode =
            trim(
                (string) Session::get(
                    'checkout_coupon_code',
                    ''
                )
            );

        try {

            $order =
                (
                    new OrderService()
                )->create(
                    $customer,
                    $items,
                    $couponCode === ''
                        ? null
                        : $couponCode,
                    (int) $shippingMethodId
                );

            $cart->clear();

            Session::forget(
                'checkout_coupon_code'
            );

            Session::flash(
                'completed_order_number',
                (string) $order->number
            );

            redirect('pedido-confirmado');

        } catch (RuntimeException $exception) {

            Session::flash(
                'errors',
                [
                    'general' => [
                        $exception->getMessage(),
                    ],
                ]
            );

            $this->saveOldInput(
                $input
            );

            redirect('checkout');

        } catch (Throwable $exception) {
            error_log(
                $exception->getMessage()
            );

            Session::flash(
                'errors',
                [
                    'general' => [
                        'No fue posible completar tu pedido. Inténtalo nuevamente.',
                    ],
                ]
            );

            $this->saveOldInput($input);
            redirect('checkout');
        }
    }

    public function applyCoupon(): void{
        $request = new Request();
        $input = $request->all();
        $code =
            strtoupper(
                trim(
                    (string) (
                        $input['coupon_code']
                        ?? ''
                    )
                )
            );

        if ($code === '') {
            Session::flash(
                'warning',
                'Escribe un código de cupón.'
            );
            redirect('checkout');
        }

        $cart = new CartService();

        if (empty($cart->items())) {

            Session::flash(
                'warning',
                'Tu carrito está vacío.'
            );

            redirect('carrito');
        }

        try {

            (
                new CouponService()
            )->preview(
                $code,
                $cart->subtotal()
            );

            Session::put(
                'checkout_coupon_code',
                $code
            );

            Session::flash(
                'success',
                "Cupón {$code} aplicado correctamente."
            );

        } catch (RuntimeException $exception) {

            Session::forget(
                'checkout_coupon_code'
            );

            Session::flash(
                'warning',
                $exception->getMessage()
            );

        } catch (Throwable $exception) {

            error_log(
                $exception->getMessage()
            );

            Session::flash(
                'warning',
                'No fue posible validar el cupón.'
            );
        }

        redirect('checkout');
    }

    public function removeCoupon(): void{
        Session::forget(
            'checkout_coupon_code'
        );

        Session::flash(
            'success',
            'El cupón fue eliminado.'
        );

        redirect('checkout');
    }

    public function success(): void{
        $number =
            session(
                'completed_order_number'
            );

        if (!$number) {
            redirect('tienda');
        }

        view(
            'checkout/success',
            [
                'title' => 'Pedido confirmado',
                'number' => $number,
                'company' => Company::first(),
            ],
            'shop'
        );
    }

    private function saveOldInput(
        array $input
    ): void {
        Session::flash(
            'old',
            [
                'name' =>
                    $input['name']
                    ?? '',

                'phone' =>
                    $input['phone']
                    ?? '',

                'email' =>
                    $input['email']
                    ?? '',

                'address' =>
                    $input['address']
                    ?? '',

                'notes' =>
                    $input['notes']
                    ?? '',

                'shipping_method_id' =>
                    $input['shipping_method_id']
                    ?? '',
            ]
        );
    }
}