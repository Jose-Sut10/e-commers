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

        print_r($user->all());

        echo "</pre>";
    }
}