<?php

namespace App\Controllers;
use Throwable;
use RuntimeException;
use Core\Controller;
use Core\Request;
use Core\Session;
use App\Models\Product;
use App\Models\Category;
use App\Support\Str;

class ProductController extends Controller{
    public function index(): void{
        view('products/index', [
            'title' => 'Productos',

            'products' =>
                Product::allWithCategory(),
        ]);
    }

    public function create(): void{
        view('products/create', [
            'title' => 'Registrar producto',

            'categories' =>
                Category::active(),
        ]);
    }

    public function store(): void{
        $request = new Request();
        $input = $request->all();

        $name = trim(
            (string) ($input['name'] ?? '')
        );

        $slug = Str::slug($name);

        $sku = strtoupper(
            trim((string) ($input['sku'] ?? ''))
        );

        $description = trim(
            (string) ($input['description'] ?? '')
        );

        $categoryId = filter_var(
            $input['category_id'] ?? null,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        $result = validator($input, [
            'category_id' => 'required|numeric',
            'name'        => 'required|min:2|max:150',
            'sku'         => 'required|min:2|max:80',
            'description' => 'max:5000',
            'price'       => 'required|numeric|min:0',
            'cost'        => 'numeric|min:0',
        ])->validate();

        if ($slug === '') {
            $result->add(
                'name',
                'No fue posible generar una URL válida para el producto.'
            );
        }

        if ($categoryId === false) {
            $result->add(
                'category_id',
                'Debes seleccionar una categoría válida.'
            );
        } else {
            $category = Category::find($categoryId);

            if (
                !$category
                || !(bool) $category->active
            ) {
                $result->add(
                    'category_id',
                    'La categoría seleccionada no está disponible.'
                );
            }
        }

        if (
            $result->first('name') === null
            && Product::findBySlug($slug)
        ) {
            $result->add(
                'name',
                'Ya existe un producto con este nombre.'
            );
        }

        if (
            $result->first('sku') === null
            && Product::findBySku($sku)
        ) {
            $result->add(
                'sku',
                'Ya existe un producto con este código SKU.'
            );
        }

        if ($result->fails()) {
            Session::flash(
                'errors',
                $result->errors()
            );

            Session::flash('old', [
                'category_id' =>
                    $input['category_id'] ?? '',

                'name' => $name,
                'sku' => $sku,

                'description' =>
                    $description,

                'price' =>
                    $input['price'] ?? '',

                'cost' =>
                    $input['cost'] ?? '',

                'stock' =>
                    $input['stock'] ?? '0',

                'active' =>
                    isset($input['active'])
                        ? '1'
                        : '0',
            ]);

            redirect('productos/crear');
        }

        $cost = trim(
            (string) ($input['cost'] ?? '')
        );

        try {
            $product = new Product([
                'category_id' =>
                    (int) $categoryId,

                'name' => $name,
                'slug' => $slug,
                'sku' => $sku,

                'description' =>
                    $description === ''
                        ? null
                        : $description,

                'price' =>
                    (float) $input['price'],

                'cost' =>
                    $cost === ''
                        ? null
                        : (float) $cost,

                'stock' => (int) $stock,

                'active' =>
                    isset($input['active'])
                        ? 1
                        : 0,
            ]);

            if (!$product->save()) {
                throw new RuntimeException(
                    'El modelo no pudo guardar el producto.'
                );
            }

            Session::flash(
                'success',
                'El producto fue registrado correctamente.'
            );

            redirect('productos');
        } catch (Throwable $exception) {
            error_log($exception->getMessage());

            Session::flash('errors', [
                'general' => [
                    'No fue posible registrar el producto.',
                ],
            ]);

            Session::flash('old', [
                'category_id' =>
                    $input['category_id'] ?? '',

                'name' => $name,
                'sku' => $sku,

                'description' =>
                    $description,

                'price' =>
                    $input['price'] ?? '',

                'cost' =>
                    $input['cost'] ?? '',

                'stock' =>
                    $input['stock'] ?? '0',

                'active' =>
                    isset($input['active'])
                        ? '1'
                        : '0',
            ]);

            redirect('productos/crear');
        }
    }

    //editar producto
    public function edit(): void{
        $id = filter_var(
            $_GET['id'] ?? null,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        if (!$id) {
            Session::flash(
                'warning',
                'El producto indicado no es válido.'
            );

            redirect('productos');
        }

        $product = Product::find($id);

        if (!$product) {
            Session::flash(
                'warning',
                'El producto no fue encontrado.'
            );

            redirect('productos');
        }

        view('products/edit', [
            'title' => 'Editar producto',
            'product' => $product,
            'categories' => Category::all(),
        ]);
    }

    //actualizar producto
    public function update(): void{
        $request = new Request();
        $input = $request->all();

        $id = filter_var(
            $input['id'] ?? null,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        if (!$id) {
            Session::flash(
                'warning',
                'El producto indicado no es válido.'
            );

            redirect('productos');
        }

        $product = Product::find($id);

        if (!$product) {
            Session::flash(
                'warning',
                'El producto no fue encontrado.'
            );

            redirect('productos');
        }

        $name = trim(
            (string) ($input['name'] ?? '')
        );

        $slug = Str::slug($name);

        $sku = strtoupper(
            trim((string) ($input['sku'] ?? ''))
        );

        $description = trim(
            (string) ($input['description'] ?? '')
        );

        $categoryId = filter_var(
            $input['category_id'] ?? null,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        $result = validator($input, [
            'category_id' => 'required|numeric',
            'name'        => 'required|min:2|max:150',
            'sku'         => 'required|min:2|max:80',
            'description' => 'max:5000',
            'price'       => 'required|numeric|min:0',
            'cost'        => 'numeric|min:0',
            'stock'       => 'required|numeric|min:0',
        ])->validate();

        /*
        * Categoría
        */
        if ($categoryId === false) {
            $result->add(
                'category_id',
                'Debes seleccionar una categoría válida.'
            );
        } else {
            $category = Category::find($categoryId);

            if (!$category) {
                $result->add(
                    'category_id',
                    'La categoría seleccionada no existe.'
                );
            }
        }

        /*
        * Slug único
        */
        if ($result->first('name') === null) {
            $existingProduct = Product::findBySlug(
                $slug
            );

            if (
                $existingProduct
                && (int) $existingProduct->id
                    !== (int) $product->id
            ) {
                $result->add(
                    'name',
                    'Ya existe otro producto con este nombre.'
                );
            }
        }

        /*
        * SKU único
        */
        if ($result->first('sku') === null) {
            $existingSku = Product::findBySku(
                $sku
            );

            if (
                $existingSku
                && (int) $existingSku->id
                    !== (int) $product->id
            ) {
                $result->add(
                    'sku',
                    'Ya existe otro producto con este código SKU.'
                );
            }
        }

        if ($result->fails()) {
            Session::flash(
                'errors',
                $result->errors()
            );

            Session::flash('old', [
                'category_id' =>
                    $input['category_id'] ?? '',

                'name' => $name,
                'sku' => $sku,

                'description' =>
                    $description,

                'price' =>
                    $input['price'] ?? '',

                'cost' =>
                    $input['cost'] ?? '',

                'stock' =>
                    $input['stock'] ?? '0',

                'active' =>
                    isset($input['active'])
                        ? '1'
                        : '0',
            ]);

            redirect(
                'productos/editar?id='
                . (int) $product->id
            );
        }

        $cost = trim(
            (string) ($input['cost'] ?? '')
        );

        try {
            $product->category_id =
                (int) $categoryId;

            $product->name = $name;

            $product->slug = $slug;

            $product->sku = $sku;

            $product->description =
                $description === ''
                    ? null
                    : $description;

            $product->price =
                (float) $input['price'];

            $product->cost =
                $cost === ''
                    ? null
                    : (float) $cost;

            $product->active =
                isset($input['active'])
                    ? 1
                    : 0;

            if (!$product->save()) {
                throw new RuntimeException(
                    'El modelo no pudo actualizar el producto.'
                );
            }

            Session::flash(
                'success',
                'El producto fue actualizado correctamente.'
            );

            redirect('productos');
        } catch (Throwable $exception) {
            error_log($exception->getMessage());

            Session::flash('errors', [
                'general' => [
                    'No fue posible actualizar el producto.',
                ],
            ]);

            redirect(
                'productos/editar?id='
                . (int) $product->id
            );
        }
    }

    //eliminar producto
    public function destroy(): void{
        $request = new Request();
        $input = $request->all();

        $id = filter_var(
            $input['id'] ?? null,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        if (!$id) {
            Session::flash(
                'warning',
                'El producto indicado no es válido.'
            );

            redirect('productos');
        }

        $product = Product::find($id);

        if (!$product) {
            Session::flash(
                'warning',
                'El producto que intentas eliminar no existe.'
            );

            redirect('productos');
        }

        try {
            if (!$product->delete()) {
                throw new RuntimeException(
                    'El modelo no pudo eliminar el producto.'
                );
            }

            Session::flash(
                'success',
                'El producto fue eliminado correctamente.'
            );

            redirect('productos');
        } catch (Throwable $exception) {
            error_log($exception->getMessage());

            Session::flash(
                'warning',
                'No fue posible eliminar el producto.'
            );

            redirect('productos');
        }
    }
}