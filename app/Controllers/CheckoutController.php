<?php
namespace App\Controllers;
use Throwable;
use RuntimeException;
use Core\Controller;
use Core\Request;
use Core\Session;
use App\Models\Company;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\CouponService;

class CheckoutController extends Controller{
    /*
     * =====================================================
     * MOSTRAR CHECKOUT
     * =====================================================
     */

    public function index(): void{
        $cart =
            new CartService();

        $items =
            $cart->items();

        /*
         * Si el carrito está vacío
         * regresamos al carrito.
         */

        if (empty($items)) {

            Session::flash(
                'warning',
                'Tu carrito está vacío.'
            );

            redirect('carrito');
        }

        /*
         * Subtotal actual.
         *
         * CartService ya toma en cuenta
         * promociones y variantes.
         */

        $subtotal = $cart->subtotal();

        /*
         * Cupón guardado en sesión.
         */

        $couponCode =
            (string) Session::get(
                'checkout_coupon_code',
                ''
            );

        /*
         * Valores predeterminados cuando
         * no existe un cupón.
         */

        $couponResult = [
            'coupon' => null,
            'code' => null,
            'discount' => 0.0,
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ];

        /*
         * Si existe un código en sesión,
         * volvemos a validarlo.
         *
         * Esto permite detectar:
         *
         * - cupones vencidos
         * - cupones desactivados
         * - límite de usos alcanzado
         * - compra mínima no cumplida
         */

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

                /*
                 * Si el cupón dejó de ser válido,
                 * lo quitamos.
                 */

                Session::forget(
                    'checkout_coupon_code'
                );


                Session::flash(
                    'warning',
                    $exception->getMessage()
                );

                /*
                 * Redirigimos para mostrar
                 * correctamente el mensaje flash.
                 */

                redirect('checkout');
            }
        }

        /*
         * Mostrar checkout.
         */

        view(
            'checkout/index',
            [
                'title' => 'Finalizar compra',
                'company' => Company::first(),
                'items' => $items,
                'subtotal' => $subtotal,
                'couponResult' => $couponResult,
            ],
            'shop'
        );
    }

    /*
     * =====================================================
     * CREAR PEDIDO
     * =====================================================
     */

    public function store(): void{
        $cart = new CartService();
        $items = $cart->items();

        /*
         * No permitir pedido con
         * carrito vacío.
         */

        if (empty($items)) {
            Session::flash(
                'warning',
                'Tu carrito está vacío.'
            );
            redirect('carrito');
        }

        $request = new Request();
        $input = $request->all();

        /*
         * =================================================
         * VALIDACIÓN
         * =================================================
         */
        $result =
            validator(
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
                ]
            );

            redirect('checkout');
        }

        /*
         * =================================================
         * NORMALIZAR DATOS
         * =================================================
         */

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

        /*
         * Recuperamos el cupón que el
         * cliente aplicó anteriormente.
         */

        $couponCode =
            trim(
                (string) Session::get(
                    'checkout_coupon_code',
                    ''
                )
            );

        /*
         * =================================================
         * CREAR PEDIDO
         * =================================================
         */

        try {

            $order =
                (
                    new OrderService()
                )->create(
                    $customer,
                    $items,
                    $couponCode === ''
                        ? null
                        : $couponCode
                );

            /*
             * Solamente vaciamos el carrito
             * si TODO el pedido se completó.
             */

            $cart->clear();

            /*
             * El cupón también se elimina
             * de la sesión después de una
             * compra exitosa.
             */

            Session::forget(
                'checkout_coupon_code'
            );

            /*
             * Guardamos temporalmente
             * el número del pedido.
             */

            Session::flash(
                'completed_order_number',
                (string) $order->number
            );

            redirect(
                'pedido-confirmado'
            );

        } catch (RuntimeException $exception) {

            /*
             * Errores esperados:
             *
             * - stock insuficiente
             * - cupón vencido
             * - cupón inválido
             * - compra mínima
             * - variante no disponible
             */

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

            /*
             * Guardamos el error técnico
             * en el log, pero no mostramos
             * detalles internos al cliente.
             */

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

            $this->saveOldInput(
                $input
            );


            redirect('checkout');
        }
    }

    /*
     * =====================================================
     * APLICAR CUPÓN
     * =====================================================
     */

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

        /*
         * Código obligatorio.
         */

        if ($code === '') {

            Session::flash(
                'warning',
                'Escribe un código de cupón.'
            );

            redirect('checkout');
        }

        /*
         * Revisar carrito.
         */

        $cart = new CartService();
        $items = $cart->items();

        if (empty($items)) {

            Session::flash(
                'warning',
                'Tu carrito está vacío.'
            );

            redirect('carrito');
        }

        /*
         * Validar cupón.
         */

        try {

            (
                new CouponService()
            )->preview(
                $code,
                $cart->subtotal()
            );


            /*
             * Si es válido, guardamos
             * solamente el código.
             *
             * Nunca almacenamos el descuento
             * calculado como dato confiable.
             */

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

            Session::forget(
                'checkout_coupon_code'
            );

            Session::flash(
                'warning',
                'No fue posible validar el cupón.'
            );
        }

        redirect('checkout');
    }

    /*
     * =====================================================
     * QUITAR CUPÓN
     * =====================================================
     */

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

    /*
     * =====================================================
     * PEDIDO CONFIRMADO
     * =====================================================
     */

    public function success(): void{
        $number =
            session(
                'completed_order_number'
            );

        /*
         * Evitamos entrar directamente
         * a esta página sin un pedido.
         */

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

    /*
     * =====================================================
     * GUARDAR CAMPOS DEL FORMULARIO
     * =====================================================
     */

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
            ]
        );
    }
}