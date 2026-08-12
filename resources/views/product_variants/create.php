<h1>Nueva variante</h1>
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
        url('productos/variantes'),
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
        <label for="name">Nombre de la variante</label>

        <input
            id="name"
            type="text"
            name="name"
            placeholder="Ej. Negro / Talla 42"
            value="<?= htmlspecialchars(
                (string) old('name'),
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
        <label for="sku"> SKU</label>

        <input
            id="sku"
            type="text"
            name="sku"
            placeholder="ZAP-NEG-42"
            value="<?= htmlspecialchars(
                (string) old('sku'),
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
            step="0.01"
            min="0"
            placeholder="<?= htmlspecialchars(
                (string) $product->price,
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            value="<?= htmlspecialchars(
                (string) old('price'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <small>
            Déjalo vacío para utilizar el precio base
            de Q <?= number_format(
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
                <?= old(
                    'active',
                    '1'
                ) === '1'
                    ? 'checked'
                    : ''
                ?>
            >
            Variante activa
        </label>
    </div>

    <button type="submit">Guardar variante</button>

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