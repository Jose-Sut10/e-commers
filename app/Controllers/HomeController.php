<?php
namespace App\Controllers;
use Core\Controller;
use Core\Auth\Auth;
use App\Services\DashboardService;

class HomeController extends Controller{
    public function index(): void{
        $dashboard =
            new DashboardService();

        view('home/index', [
            'title' =>
                'Panel principal',

            'user' =>
                Auth::user(),

            'statistics' =>
                $dashboard->statistics(),

            'recentOrders' =>
                $dashboard->recentOrders(),

            'lowStockProducts' =>
                $dashboard->lowStockProducts(),
        ]);
    }
}