<?php
$success = session('success');
$warning = session('warning');
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


<h1>Editar producto</h1>


<?php if (error('general')): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars(
            (string) error('general'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </div>
<?php endif; ?>


<!-- =====================================================
     FORMULARIO DE INFORMACIÓN DEL PRODUCTO
===================================================== -->

<form
    method="POST"
    action="<?= htmlspecialchars(
        url('productos/actualizar'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>"
>
    <?= csrf_field() ?>

    <input
        type="hidden"
        name="id"
        value="<?= (int) $product->id ?>"
    >


    <!-- Categoría -->

    <div>
        <label for="category_id">
            Categoría
        </label>

        <?php
        $selectedCategory = old(
            'category_id',
            (string) $product->category_id
        );
        ?>

        <select
            id="category_id"
            name="category_id"
        >
            <option value="">
                Selecciona una categoría
            </option>

            <?php foreach ($categories as $category): ?>

                <option
                    value="<?= (int) $category->id ?>"
                    <?= (string) $selectedCategory
                        === (string) $category->id
                            ? 'selected'
                            : ''
                    ?>
                >
                    <?= htmlspecialchars(
                        (string) $category->name,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                    <?= !(bool) $category->active
                        ? ' (Inactiva)'
                        : ''
                    ?>
                </option>

            <?php endforeach; ?>
        </select>

        <?php if (error('category_id')): ?>
            <small class="form-error">
                <?= htmlspecialchars(
                    (string) error('category_id'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </small>
        <?php endif; ?>
    </div>


    <!-- Nombre -->

    <div>
        <label for="name">
            Nombre
        </label>

        <input
            id="name"
            type="text"
            name="name"
            value="<?= htmlspecialchars(
                (string) old(
                    'name',
                    $product->name
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <?php if (error('name')): ?>
            <small class="form-error">
                <?= htmlspecialchars(
                    (string) error('name'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </small>
        <?php endif; ?>
    </div>


    <!-- SKU -->

    <div>
        <label for="sku">
            SKU
        </label>

        <input
            id="sku"
            type="text"
            name="sku"
            value="<?= htmlspecialchars(
                (string) old(
                    'sku',
                    $product->sku
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <?php if (error('sku')): ?>
            <small class="form-error">
                <?= htmlspecialchars(
                    (string) error('sku'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </small>
        <?php endif; ?>
    </div>


    <!-- Descripción -->

    <div>
        <label for="description">
            Descripción
        </label>

        <textarea
            id="description"
            name="description"
            rows="5"
        ><?= htmlspecialchars(
            (string) old(
                'description',
                $product->description
            ),
            ENT_QUOTES,
            'UTF-8'
        ) ?></textarea>

        <?php if (error('description')): ?>
            <small class="form-error">
                <?= htmlspecialchars(
                    (string) error('description'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </small>
        <?php endif; ?>
    </div>

    <!-- Precio -->

    <div>
        <label for="price">
            Precio de venta
        </label>

        <input
            id="price"
            type="number"
            name="price"
            step="0.01"
            min="0"
            value="<?= htmlspecialchars(
                (string) old(
                    'price',
                    $product->price
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <?php if (error('price')): ?>
            <small class="form-error">
                <?= htmlspecialchars(
                    (string) error('price'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </small>
        <?php endif; ?>
    </div>

    <!-- Costo -->

    <div>
        <label for="cost">
            Costo
        </label>

        <input
            id="cost"
            type="number"
            name="cost"
            step="0.01"
            min="0"
            value="<?= htmlspecialchars(
                (string) old(
                    'cost',
                    $product->cost
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <?php if (error('cost')): ?>
            <small class="form-error">
                <?= htmlspecialchars(
                    (string) error('cost'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </small>
        <?php endif; ?>
    </div>


    <!-- Estado -->

    <?php
    $activeValue = old(
        'active',
        (bool) $product->active
            ? '1'
            : '0'
    );
    ?>

    <div>
        <label>
            <input
                type="checkbox"
                name="active"
                value="1"
                <?= $activeValue === '1'
                    ? 'checked'
                    : ''
                ?>
            >

            Producto activo
        </label>
    </div>


    <!-- Acciones -->

    <div>
        <button type="submit">
            Guardar cambios
        </button>

        <a href="<?= htmlspecialchars(
            url('productos'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>">
            Cancelar
        </a>
    </div>

</form>


<!-- =====================================================
     INVENTARIO
===================================================== -->

<hr>

<h2>Inventario</h2>

<p>
    Existencias actuales:
    <strong>
        <?= (int) $product->stock ?>
    </strong>
</p>

<a href="<?= htmlspecialchars(
    url(
        'inventario?id='
        . (int) $product->id
    ),
    ENT_QUOTES,
    'UTF-8'
) ?>">
    Administrar inventario
</a>


<!-- =====================================================
     SUBIR IMAGEN
===================================================== -->

<hr>

<h2>Imágenes del producto</h2>

<form
    method="POST"
    enctype="multipart/form-data"
    action="<?= htmlspecialchars(
        url('productos/imagen'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>"
>
    <?= csrf_field() ?>

    <input
        type="hidden"
        name="product_id"
        value="<?= (int) $product->id ?>"
    >

    <div>
        <label for="image">
            Seleccionar imagen
        </label>

        <input
            id="image"
            type="file"
            name="image"
            accept="image/jpeg,image/png,image/webp"
            required
        >
    </div>

    <p>JPG, PNG o WebP. Máximo 5 MB.</p>

    <button type="submit">
        Subir imagen
    </button>
</form>


<!-- =====================================================
     GALERÍA DE IMÁGENES
===================================================== -->

<?php if (!empty($images)): ?>

    <div class="product-images">

        <?php foreach ($images as $image): ?>

            <div class="product-image">

                <img
                    src="<?= htmlspecialchars(
                        asset(
                            (string) $image->path
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    alt="<?= htmlspecialchars(
                        (string) $product->name,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    width="180"
                >

                <?php if ((bool) $image->is_primary): ?>

                    <p><strong>Imagen principal</strong></p>

                <?php else: ?>

                    <!-- Hacer principal -->

                    <form
                        method="POST"
                        action="<?= htmlspecialchars(
                            url(
                                'productos/imagen/principal'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >
                        <?= csrf_field() ?>

                        <input
                            type="hidden"
                            name="image_id"
                            value="<?= (int) $image->id ?>"
                        >

                        <button type="submit">
                            Hacer principal
                        </button>
                    </form>

                <?php endif; ?>

                <!-- Eliminar imagen -->

                <form
                    method="POST"
                    action="<?= htmlspecialchars(
                        url(
                            'productos/imagen/eliminar'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    onsubmit="return confirm(
                        '¿Estás seguro de eliminar esta imagen?'
                    );"
                >
                    <?= csrf_field() ?>

                    <input
                        type="hidden"
                        name="image_id"
                        value="<?= (int) $image->id ?>"
                    >

                    <button type="submit">
                        Eliminar
                    </button>
                </form>

            </div>

        <?php endforeach; ?>

    </div>

<?php else: ?>
    <p>Este producto todavía no tiene imágenes.</p>
<?php endif; ?>