<?php

namespace App\Controllers;
use Throwable;
use RuntimeException;
use Core\Controller;
use Core\Request;
use Core\Session;
use App\Models\Category;
use App\Support\Str;

class CategoryController extends Controller{
    public function index(): void{
        view('categories/index', [
            'title' => 'Categorías',
            'categories' => Category::all(),
        ]);
    }

    public function create(): void{
        view('categories/create', [
            'title' => 'Registrar categoría',
        ]);
    }

    public function store(): void{
        $request = new Request();
        $input = $request->all();

        $name = trim(
            (string) ($input['name'] ?? '')
        );

        $slug = Str::slug($name);

        $description = trim(
            (string) ($input['description'] ?? '')
        );

        $result = validator($input, [
            'name' => 'required|min:2|max:120',
            'description' => 'max:1000',
        ])->validate();

        if ($slug === '') {
            $result->add(
                'name',
                'No fue posible generar una URL válida para la categoría.'
            );
        }

        if (
            $result->first('name') === null
            && Category::findBySlug($slug)
        ) {
            $result->add(
                'name',
                'Ya existe una categoría con este nombre.'
            );
        }

        if ($result->fails()) {
            Session::flash(
                'errors',
                $result->errors()
            );

            Session::flash('old', [
                'name' => $name,
                'description' => $description,
                'active' => isset($input['active'])
                    ? '1'
                    : '0',
            ]);

            redirect('categorias/crear');
        }

        try {
            $category = new Category([
                'name' => $name,
                'slug' => $slug,

                'description' => $description === ''
                    ? null
                    : $description,

                'active' => isset($input['active'])
                    ? 1
                    : 0,
            ]);

            if (!$category->save()) {
                throw new RuntimeException(
                    'El modelo no pudo guardar la categoría.'
                );
            }

            Session::flash(
                'success',
                'La categoría fue registrada correctamente.'
            );

            redirect('categorias');
        } catch (Throwable $exception) {
            error_log($exception->getMessage());

            Session::flash('errors', [
                'general' => [
                    'No fue posible registrar la categoría.',
                ],
            ]);

            Session::flash('old', [
                'name' => $name,
                'description' => $description,
                'active' => isset($input['active'])
                    ? '1'
                    : '0',
            ]);
            redirect('categorias/crear');
        }
    }
}