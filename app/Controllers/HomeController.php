<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $user = new User();

        echo "<pre>";

        print_r(
            $user->where('estado',1)
                 ->orderBy('nombre')
                 ->limit(10)
                 ->get()
        );

        echo "</pre>";
    }
}