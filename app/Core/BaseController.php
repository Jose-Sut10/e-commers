<?php

namespace Core;

abstract class BaseController extends Controller
{
    protected string $viewPath = '';
    protected string $route = '';
    public function index()
    {
        view($this->viewPath . '/index', [
            'title' => 'Listado'
        ]);
    }

    public function create()
    {
        view($this->viewPath . '/create', [
            'title' => 'Nuevo registro'
        ]);
    }

    public function store()
    {

    }

    public function edit()
    {

    }

    public function update()
    {

    }

    public function destroy()
    {

    }
}