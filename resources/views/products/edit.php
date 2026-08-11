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

    <div>
        <label for="category_id">
            Categoría
        </label>

        <select
            id="category_id"
            name="category_id"
        >
            <?php
            $selectedCategory = old(
                'category_id',
                (string) $product->category_id
            );
            ?>

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
    </div>

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
    </div>

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
    </div>

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
    </div>

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
    </div>

    <div>
        <label for="stock">
            Existencias
        </label>

        <input
            id="stock"
            type="number"
            name="stock"
            min="0"
            step="1"
            value="<?= htmlspecialchars(
                (string) old(
                    'stock',
                    $product->stock
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >
    </div>

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
</form>