<h1>Editar variante</h1>

<p>
    Producto:
    <strong>
        <?= htmlspecialchars(
            (string) $product->name,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </strong>
</p>

<p>
    Stock actual:
    <strong>
        <?= (int) $variant->stock ?>
    </strong>
</p>

<p>
    El stock se administra desde el módulo
    de Inventario.
</p>

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
        url(
            'productos/variantes/actualizar'
        ),
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

    <input
        type="hidden"
        name="variant_id"
        value="<?= (int) $variant->id ?>"
    >

    <div>
        <label for="name">Nombre de la variante</label>

        <input
            id="name"
            type="text"
            name="name"
            value="<?= htmlspecialchars(
                (string) old(
                    'name',
                    $variant->name
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

    <div>
        <label for="sku">SKU</label>
        <input
            id="sku"
            type="text"
            name="sku"
            value="<?= htmlspecialchars(
                (string) old(
                    'sku',
                    $variant->sku
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

    <div>
        <label for="price">Precio especial</label>
        <input
            id="price"
            type="number"
            name="price"
            min="0"
            step="0.01"
            value="<?= htmlspecialchars(
                (string) old(
                    'price',
                    $variant->price ?? ''
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <small>

            Déjalo vacío para utilizar
            el precio base de:

            Q <?= number_format(
                (float) $product->price,
                2
            ) ?>.

        </small>

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

    <div>
        <label>

            <input
                type="checkbox"
                name="active"
                value="1"

                <?= (string) old(
                    'active',
                    (bool) $variant->active
                        ? '1'
                        : '0'
                ) === '1'
                    ? 'checked'
                    : ''
                ?>
            >
            Variante activa

        </label>

    </div>

    <button type="submit">Guardar cambios</button>

    <a href="<?= htmlspecialchars(
        url(
            'productos/variantes?id='
            . (int) $product->id
        ),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        Cancelar
    </a>
</form>