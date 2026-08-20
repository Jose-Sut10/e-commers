<h1>Productos</h1>

<?php
$success = session('success');
$warning = session('warning');
$products = $paginator->items();
?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars(
            (string) $success,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </div>
<?php endif; ?>

<?php if ($warning): ?>
    <div class="alert alert-warning">
        <?= htmlspecialchars(
            (string) $warning,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </div>
<?php endif; ?>

<div class="page-actions">
    <a href="<?= htmlspecialchars(
        url('productos/crear'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        Nuevo producto
    </a>
</div>

<!-- FILTROS -->
<form
    method="GET"
    action="<?= htmlspecialchars(
        url('productos'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>"
    class="filter-form"
>
    <div>

        <label for="q"> Buscar</label>

        <input
            id="q"
            type="search"
            name="q"
            placeholder="Nombre, SKU..."
            value="<?= htmlspecialchars(
                (string) $filters['q'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

    </div>

    <div>
        <label for="category_id">Categoría</label>

        <select
            id="category_id"
            name="category_id"
        >
            <option value="">
                Todas
            </option>

            <?php foreach (
                $categories as $category
            ): ?>
                <option
                    value="<?= (int) $category->id ?>"

                    <?= (string)
                        $filters['category_id']
                        ===
                        (string)
                        $category->id
                            ? 'selected'
                            : ''
                    ?>
                >
                    <?= htmlspecialchars(
                        (string) $category->name,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>

        <label for="active">Estado</label>

        <select
            id="active"
            name="active"
        >
            <option value=""> Todos</option>

            <option
                value="1"
                <?= $filters['active'] === '1'
                    ? 'selected'
                    : ''
                ?>
            >
                Activos
            </option>

            <option
                value="0"
                <?= $filters['active'] === '0'
                    ? 'selected'
                    : ''
                ?>
            >
                Inactivos
            </option>
        </select>
    </div>

    <div>
        <label for="stock">Inventario</label>

        <select
            id="stock"
            name="stock"
        >
            <option value=""> Todos</option>

            <option
                value="out"
                <?= $filters['stock'] === 'out'
                    ? 'selected'
                    : ''
                ?>
            >
                Agotados
            </option>

            <option
                value="low"
                <?= $filters['stock'] === 'low'
                    ? 'selected'
                    : ''
                ?>
            >
                Stock bajo
            </option>

            <option
                value="available"
                <?= $filters['stock'] === 'available'
                    ? 'selected'
                    : ''
                ?>
            >
                Más de 5 unidades
            </option>
        </select>

    </div>

    <div class="filter-buttons">

        <button type="submit"> Filtrar</button>

        <a href="<?= htmlspecialchars(
            url('productos'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>">
            Limpiar
        </a>
    </div>
</form>

<p class="pagination-summary">
    Mostrando
    <?= $paginator->from() ?>
    a
    <?= $paginator->to() ?>
    de
    <?= $paginator->total() ?>
    productos.
</p>

<?php if (empty($products)): ?>
    <p>
        No encontramos productos
        con los filtros seleccionados.
    </p>
<?php else: ?>

<table>
    <thead>
        <tr>
            <th>Imagen</th>
            <th>Producto</th>
            <th>SKU</th>
            <th>Categoría</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>

    <tbody>

    <?php foreach (
        $products as $product
    ): ?>
        <tr>
            <td>
                <?php if (
                    $product->image_path
                ): ?>

                    <img
                        src="<?= htmlspecialchars(
                            asset(
                                (string)
                                $product->image_path
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        alt=""
                        width="55"
                        height="55"
                        style="
                            object-fit:cover;
                            border-radius:6px;
                        "
                    >

                <?php else: ?>
                    -
                <?php endif; ?>
            </td>

            <td>
                <strong>
                    <?= htmlspecialchars(
                        (string) $product->name,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>
            </td>

            <td>

                <?= htmlspecialchars(
                    (string) $product->sku,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </td>

            <td>
                <?= htmlspecialchars(
                    (string)
                    $product->category_name,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </td>

            <td>
                Q <?= number_format(
                    (float) $product->price,
                    2
                ) ?>
            </td>

            <td>
                <strong>
                    <?= (int)
                        $product->available_stock
                    ?>
                </strong>
            </td>

            <td>

                <?= (bool) $product->active
                    ? 'Activo'
                    : 'Inactivo'
                ?>
            </td>

            <td>
                <a href="<?= htmlspecialchars(
                    url(
                        'productos/editar?id='
                        . (int) $product->id
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>">
                    Editar
                </a>

                |

                <a href="<?= htmlspecialchars(
                    url(
                        'productos/variantes?id='
                        . (int) $product->id
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>">
                    Variantes
                </a>

                |

                <a href="<?= htmlspecialchars(
                    url(
                        'inventario?id='
                        . (int) $product->id
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>">
                    Inventario
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php
$paginationPath ='productos';
$paginationQuery =$filters;
require BASE_PATH
    . '/resources/views/partials/pagination.php';

?>