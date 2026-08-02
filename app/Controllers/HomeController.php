<?php

namespace App\Controllers;

use Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $result = validator(
            [
                'name'  => 'Jo',
                'email' => 'correo-invalido',
                'tax'   => 'abc',
            ],
            [
                'name'  => 'required|min:3|max:150',
                'email' => 'required|email',
                'tax'   => 'numeric|min:0|max:100',
            ]
        )->validate();

        echo '<pre>';
        print_r($result->errors());
        echo '</pre>';
    }
}