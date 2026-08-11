<h1>Registrar producto</h1>
<?php if (error('general')): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars(
            (string) error('general'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </div>
<?php endif; ?>

<?php if (empty($categories)): ?>

    <div class="alert alert-warning">
        Debes registrar y activar una categoría antes de crear productos.
    </div>

    <a href="<?= htmlspecialchars(
        url('categorias/crear'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        Registrar categoría
    </a>

<?php else: ?>

<form
    method="POST"
    action="<?= htmlspecialchars(
        url('productos'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
    <?= csrf_field() ?>

    <div>
        <label for="category_id">
            Categoría
        </label>

        <select
            id="category_id"
            name="category_id">
            <option value="">
                Selecciona una categoría
            </option>

            <?php foreach ($categories as $category): ?>
                <option
                    value="<?= (int) $category->id ?>"
                    <?= (string) old('category_id')
                        === (string) $category->id
                            ? 'selected'
                            : ''
                    ?>>
                    <?= htmlspecialchars(
                        (string) $category->name,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
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
            Nombre del producto
        </label>

        <input
            id="name"
            type="text"
            name="name"
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
        <label for="sku">
            Código SKU
        </label>

        <input
            id="sku"
            type="text"
            name="sku"
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
        <label for="description">
            Descripción
        </label>

        <textarea
            id="description"
            name="description"
            rows="5"
        ><?= htmlspecialchars(
            (string) old('description'),
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
                (string) old('price'),
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
                (string) old('cost'),
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
                (string) old('stock', '0'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <?php if (error('stock')): ?>
            <small class="form-error">
                <?= htmlspecialchars(
                    (string) error('stock'),
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
                <?= old('active', '1') === '1'
                    ? 'checked'
                    : ''
                ?>
            >

            Producto activo
        </label>
    </div>

    <button type="submit">
        Guardar producto
    </button>

    <a href="<?= htmlspecialchars(
        url('productos'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        Cancelar
    </a>
</form>

<?php endif; ?>