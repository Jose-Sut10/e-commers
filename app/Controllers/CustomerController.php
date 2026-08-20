<?php
namespace App\Controllers;
use Core\Controller;
use Core\Session;
use App\Models\Customer;

class CustomerController extends Controller{
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

            'active' =>
                trim(
                    (string) (
                        $_GET['active']
                        ?? ''
                    )
                ),

            'orders' =>
                trim(
                    (string) (
                        $_GET['orders']
                        ?? ''
                    )
                ),
        ];

        view(
            'customers/index',
            [
                'title' =>'Clientes',
                'paginator' =>
                    Customer::paginateAdmin(
                        $filters,
                        (int) $page,
                        15
                    ),
                'filters' => $filters,
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
                'El cliente indicado no es válido.'
            );

            redirect('clientes');
        }

        $customer =
            Customer::findWithStats(
                (int) $id
            );

        if (!$customer) {
            Session::flash(
                'warning',
                'El cliente no fue encontrado.'
            );

            redirect('clientes');
        }

        view(
            'customers/show',
            [
                'title' =>
                    'Cliente - '
                    . $customer['name'],

                'customer' => $customer,

                'orders' =>
                    Customer::orders(
                        (int) $customer['id']
                    ),
            ]
        );
    }
}