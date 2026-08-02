<?php
namespace App\Controllers;
use Throwable;
use Core\Controller;
use Core\Request;
use Core\Session;
use App\Models\Company;

class CompanyController extends Controller{
    public function index(): void{
        view('company/index', [
            'title' => 'Empresa'
        ]);
    }

    public function create(): void{
        view('company/create', [
            'title' => 'Registrar empresa'
        ]);
    }

    public function store(): void{
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

        /*
         * Solo guardamos los campos permitidos.
         * No enviamos directamente todo el contenido de $_POST al modelo.*/
        $now = date('Y-m-d H:i:s');

        $data = [
            'name'       => trim((string) ($input['name'] ?? '')),
            'email'      => $this->nullableString($input['email'] ?? null),
            'tax'        => $this->taxValue($input['tax'] ?? null),
            'created_at' => $now,
            'updated_at' => $now,
        ];

        try {
            $company = new Company($data);

            if (!$company->save()) {
                throw new \RuntimeException(
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
            ]
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
}