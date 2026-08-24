<?php
namespace App\Controllers;
use Throwable;
use Core\Controller;
use Core\Request;
use Core\Session;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderService;

class OrderController extends Controller{
    public function index(): void{
        $page = filter_var(
            $_GET['page'] ?? 1,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        if (!$page) {
            $page = 1;
        }

        $filters = [

            'q' =>
                trim(
                    (string) (
                        $_GET['q']
                        ?? ''
                    )
                ),

            'status' =>
                trim(
                    (string) (
                        $_GET['status']
                        ?? ''
                    )
                ),

            'date_from' =>
                trim(
                    (string) (
                        $_GET['date_from']
                        ?? ''
                    )
                ),

            'date_to' =>
                trim(
                    (string) (
                        $_GET['date_to']
                        ?? ''
                    )
                ),
        ];

        view(
            'orders/index',
            [
                'title' =>
                    'Pedidos',

                'paginator' =>
                    Order::paginateAdmin(
                        $filters,
                        (int) $page,
                        15
                    ),

                'filters' =>
                    $filters,
            ]
        );
    }

    public function show(): void{
        $id = filter_var(
            $_GET['id'] ?? null,
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
                'El pedido indicado no es válido.'
            );

            redirect('pedidos');
        }

        $order =
            Order::find($id);

        if (!$order) {
            Session::flash(
                'warning',
                'El pedido no fue encontrado.'
            );

            redirect('pedidos');
        }

        view('orders/show', [
            'title' =>
                "Pedido {$order->number}",

            'order' =>
                $order,

            'items' =>
                OrderItem::forOrder(
                    (int) $order->id
                ),
        ]);
    }

    public function updateStatus(): void{
        $request =
            new Request();

        $input =
            $request->all();

        $id = filter_var(
            $input['id'] ?? null,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        $status = trim(
            (string) (
                $input['status'] ?? ''
            )
        );

        if (!$id || $status === '') {
            Session::flash(
                'warning',
                'Los datos del pedido no son válidos.'
            );

            redirect('pedidos');
        }

        try {
            $order =
                (
                    new OrderService()
                )->changeStatus(
                    (int) $id,
                    $status
                );

            Session::flash(
                'success',
                "El pedido {$order->number} fue actualizado correctamente."
            );

            redirect(
                'pedidos/ver?id='
                . (int) $order->id
            );

        } catch (Throwable $exception) {

            error_log(
                $exception->getMessage()
            );

            Session::flash(
                'warning',
                $exception->getMessage()
            );

            redirect(
                'pedidos/ver?id='
                . (int) $id
            );
        }
    }

    public function updatePaymentStatus(): void{
        $request = new Request();
        $input = $request->all();

        $orderId =
            filter_var(
                $input['order_id']
                ?? null,
                FILTER_VALIDATE_INT,
                [
                    'options' => [
                        'min_range' => 1,
                    ],
                ]
            );

        $status =
            trim(
                (string) (
                    $input['payment_status']
                    ?? ''
                )
            );

        if (!$orderId) {
            Session::flash(
                'warning',
                'El pedido indicado no es válido.'
            );
            redirect('pedidos');
        }

        try {

            (
                new OrderService()
            )->changePaymentStatus(
                (int) $orderId,
                $status
            );

            Session::flash(
                'success',
                $status === 'paid'
                    ? 'El pago fue registrado correctamente.'
                    : 'El pago volvió a estado pendiente.'
            );

        } catch (RuntimeException $exception) {

            Session::flash( 'warning', $exception->getMessage());

        } catch (Throwable $exception) {
            error_log(
                $exception->getMessage()
            );

            Session::flash(
                'warning',
                'No fue posible actualizar el pago.'
            );
        }

        redirect(
            'pedidos/ver?id='
            . (int) $orderId
        );
    }
}