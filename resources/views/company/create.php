<h1>Registrar empresa</h1>

<?php if (error('general')): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars(
            error('general'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </div>
<?php endif; ?>

<form
    method="POST"
    action="<?= url('empresa') ?>">
    <div>
        <label for="name">Nombre de la empresa</label>

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
                    error('name'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </small>
        <?php endif; ?>
    </div>

    <div>
        <label for="email">Correo electrónico</label>

        <input
            id="email"
            type="email"
            name="email"
            value="<?= htmlspecialchars(
                (string) old('email'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>">

        <?php if (error('email')): ?>
            <small class="form-error">
                <?= htmlspecialchars(
                    error('email'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </small>
        <?php endif; ?>
    </div>

    <div>
        <label for="tax">Impuesto</label>
        <input
            id="tax"
            type="number"
            name="tax"
            step="0.01"
            min="0"
            max="100"
            value="<?= htmlspecialchars(
                (string) old('tax', '12'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>">

        <?php if (error('tax')): ?>
            <small class="form-error">
                <?= htmlspecialchars(
                    error('tax'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </small>
        <?php endif; ?>
    </div>

    <button type="submit">Guardar empresa</button>
</form>