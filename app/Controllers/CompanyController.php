<?php
namespace App\Controllers;
use Throwable;
use RuntimeException;
use Core\Controller;
use Core\Request;
use Core\Session;
use App\Models\Company;

class CompanyController extends Controller{
    public function index(): void{
        $company = Company::first();
        view('company/index', [
            'title'   => 'Empresa',
            'company' => $company,
        ]);
    }

    public function create(): void{
        /** En este CMS solo registraremos una empresa principal.*/
        if (Company::first()) {
            Session::flash(
                'warning',
                'Ya existe una empresa registrada.'
            );
            redirect('empresa');
        }
        view('company/create', [
            'title' => 'Registrar empresa',
        ]);
    }

    public function store(): void{
        /*
         * Evitamos que alguien registre otra empresa
         * enviando directamente una petición POST.
         */
        if (Company::first()) {
            Session::flash(
                'warning',
                'Ya existe una empresa registrada.'
            );
            redirect('empresa');
        }

        $request = new Request();
        $input = $request->all();

        $result = validator($input, [
            'name'  => 'required|min:3|max:150',
            'email' => 'email|max:150',
            'tax'   => 'numeric|min:0|max:100',
        ])->validate();

        if ($result->fails()) {
            Session::flash('errors', $result->errors());
            Session::flash('old', $input);

            redirect('empresa/crear');
        }

        $now = date('Y-m-d H:i:s');

        $data = [
            'name'       => trim((string) ($input['name'] ?? '')),
            'email'      => $this->nullableString(
                $input['email'] ?? null
            ),
            'tax'        => $this->taxValue(
                $input['tax'] ?? null
            ),
            'created_at' => $now,
            'updated_at' => $now,
        ];

        try {
            $company = new Company($data);

            if (!$company->save()) {
                throw new RuntimeException(
                    'El modelo no pudo guardar la empresa.'
                );
            }

            Session::flash(
                'success',
                'La empresa fue registrada correctamente.'
            );

            redirect('empresa');
            } catch (Throwable $exception) {
            error_log($exception->getMessage());

            Session::flash('errors', [
                'general' => [
                    'No fue posible guardar la empresa.'
                ],
            ]);
            Session::flash('old', $input);
            redirect('empresa/crear');
        }
    }

    private function nullableString(mixed $value): ?string{
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function taxValue(mixed $value): float{
        if ($value === null || $value === '') {
            return 12.00;
        }
        return (float) $value;
    }

    public function edit(): void{
        $company = Company::first();

        if (!$company) {
            Session::flash(
                'warning',
                'Primero debes registrar una empresa.'
            );
            redirect('empresa/crear');
        }

        view('company/edit', [
            'title'   => 'Editar empresa',
            'company' => $company,
        ]);
    }

    public function update(): void{
        $company = Company::first();

        if (!$company) {
            Session::flash(
                'warning',
                'No se encontró una empresa para actualizar.'
            );
            redirect('empresa/crear');
        }

        $request = new Request();
        $input = $request->all();

        $result = validator($input, [
            'name'  => 'required|min:3|max:150',
            'email' => 'email|max:150',
            'tax'   => 'numeric|min:0|max:100',
        ])->validate();

        if ($result->fails()) {
            Session::flash(
                'errors',
                $result->errors()
            );
            Session::flash('old', $input);
            redirect('empresa/editar');
        }

        try {
            $company->name = trim(
                (string) ($input['name'] ?? '')
            );

            $company->email = $this->nullableString(
                $input['email'] ?? null
            );

            $company->tax = $this->taxValue(
                $input['tax'] ?? null
            );

            $company->updated_at = date(
                'Y-m-d H:i:s'
            );

            if (!$company->save()) {
                throw new RuntimeException(
                    'El modelo no pudo actualizar la empresa.'
                );
            }

            Session::flash(
                'success',
                'La información de la empresa fue actualizada correctamente.'
            );

            redirect('empresa');
        } catch (Throwable $exception) {
            error_log($exception->getMessage());

            Session::flash('errors', [
                'general' => [
                    'No fue posible actualizar la empresa.'
                ],
            ]);
            Session::flash('old', $input);
            redirect('empresa/editar');
        }
    }
}