<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $usuarios = User::query()
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get();

        echo "<pre>";
        print_r($usuarios);
        echo "</pre>";
    }
}