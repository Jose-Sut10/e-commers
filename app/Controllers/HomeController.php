<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;

class HomeController extends Controller
{
    public function index(){
        dump(config('app'));

        view('dashboard/index', [
            'title' => 'Dashboard'
        ]);
    }
}