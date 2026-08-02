<?php
namespace App\Controllers;
use Core\Controller;
use Core\Request;
use Core\Session;

class CompanyController extends Controller{
    public function create(): void{
        view('company/create', [
            'title' => 'Registrar empresa'
        ]);
    }

    public function store(): void{
        $request = new Request();

        $data = $request->all();

        $result = validator($data, [
            'name' => 'required|min:3|max:150',
            'email' => 'email|max:150',
            'tax' => 'numeric|min:0|max:100',
        ])->validate();

        if ($result->fails()) {
            Session::flash(
                'errors',
                $result->errors()
            );

            Session::flash('old', $data);
            redirect('company/create');
        }
        echo 'La información es válida.';
    }
}