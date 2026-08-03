<h1>Registrar categoría</h1>

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
        url('categorias'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
    <?= csrf_field() ?>

    <div>
        <label for="name">
            Nombre
        </label>

        <input
            id="name"
            type="text"
            name="name"
            value="<?= htmlspecialchars(
                (string) old('name'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>">

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
        <label for="description">Descripción</label>

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
        <label>
            <input
                type="checkbox"
                name="active"
                value="1"
                <?= old('active', '1') === '1'
                    ? 'checked'
                    : ''
                ?>>
            Categoría activa
        </label>
    </div>

    <button type="submit">Guardar categoría</button>

    <a href="<?= htmlspecialchars(
        url('categorias'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        Cancelar
    </a>
</form>