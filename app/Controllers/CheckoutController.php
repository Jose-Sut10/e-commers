<?php
namespace App\Controllers;
use Throwable;
use RuntimeException;
use Core\Controller;
use Core\Request;
use Core\Session;
use App\Models\Company;
use App\Models\ShippingMethod;
use App\Models\PaymentMethod;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\CouponService;

class CheckoutController extends Controller{
    /*CHECKOUT */

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

        /*SUBTOTAL*/
        $subtotal =
            $cart->subtotal();


        /* CUPÓN*/

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

        /*MÉTODOS DE ENVÍO*/

        $shippingMethods = ShippingMethod::activeOrdered();

        /* MÉTODOS DE PAGO*/

        $paymentMethods = PaymentMethod::activeOrdered();

        /*VISTA*/

        view(
            'checkout/index',
            [
                'title' => 'Finalizar compra',
                'company' => Company::first(),
                'items' => $items,
                'subtotal' => $subtotal,
                'couponResult' => $couponResult,
                'shippingMethods' => $shippingMethods,
                'paymentMethods' => $paymentMethods,
            ],
            'shop'
        );
    }

    /*CREAR PEDIDO*/
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

        /*VALIDACIÓN GENERAL*/
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
                    'payment_method_id' => 'required|numeric|min:1',
                ]
            )->validate();

        if ($result->fails()) {
            Session::flash(
                'errors', $result->errors()
            );

            $this->saveOldInput(
                $input
            );
            redirect('checkout');
        }

        /* VALIDAR MÉTODO DE ENVÍO*/

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
            Session::flash(
                'errors',
                [
                    'shipping_method_id' => [
                        'Selecciona un método de envío válido.',
                    ],
                ]
            );
            $this->saveOldInput($input);
            redirect('checkout');
        }

        /*VALIDAR MÉTODO DE PAGO*/

        $paymentMethodId =
            filter_var(
                $input['payment_method_id']
                ?? null,
                FILTER_VALIDATE_INT,
                [
                    'options' => [
                        'min_range' => 1,
                    ],
                ]
            );

        if (!$paymentMethodId) {
            Session::flash(
                'errors',
                [
                    'payment_method_id' => [
                        'Selecciona un método de pago válido.',
                    ],
                ]
            );
            $this->saveOldInput($input);
            redirect('checkout');
        }

        /* DATOS DEL CLIENTE*/
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
                    : mb_strtolower(
                        $email
                    ),

            'address' =>
                trim(
                    (string) $input['address']
                ),

            'notes' =>
                $notes === ''
                    ? null
                    : $notes,
        ];

        /*CUPÓN*/
        $couponCode =
            trim(
                (string) Session::get(
                    'checkout_coupon_code',
                    ''
                )
            );


        $paymentProofFile = $_FILES['payment_proof'] ?? null;
        /*CREAR PEDIDO*/

        try {
            $order =
                (new OrderService())->create(
                    $customer,
                    $items,
                    $couponCode === ''
                        ? null
                        : $couponCode,
                    (int) $shippingMethodId,
                    (int) $paymentMethodId,
                    $paymentProofFile
                );

            /*
             * Solo vaciamos el carrito
             * después de crear correctamente
             * el pedido.
             */
            $cart->clear();

            /*El cupón deja de ser necesario.*/

            Session::forget(
                'checkout_coupon_code'
            );

            /*
             * Guardamos temporalmente
             * el número de pedido.
             */
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
            $this->saveOldInput($input);
            redirect('checkout');
        } catch (Throwable $exception) {
            error_log($exception->getMessage());

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

    /*APLICAR CUPÓN*/
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

            Session::forget('checkout_coupon_code');

            Session::flash(
                'warning',
                'No fue posible validar el cupón.'
            );
        }
        redirect('checkout');
    }

    /*QUITAR CUPÓN*/
    public function removeCoupon(): void{
        Session::forget('checkout_coupon_code');

        Session::flash(
            'success',
            'El cupón fue eliminado.'
        );
        redirect('checkout');
    }

    /*PEDIDO CONFIRMADO*/

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

    /*GUARDAR DATOS ANTERIORES*/

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

                'payment_method_id' =>
                    $input['payment_method_id']
                    ?? '',
            ]
        );
    }
}