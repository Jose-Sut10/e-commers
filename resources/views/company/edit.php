<h1>Editar empresa</h1>
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
        url('empresa/actualizar'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">

    <?= csrf_field() ?>
    
    <div>
        <label for="name">Nombre de la empresa</label>

        <input
            id="name"
            type="text"
            name="name"
            value="<?= htmlspecialchars(
                (string) old(
                    'name',
                    $company->name
                ),
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
        <label for="email">Correo electrónico</label>

        <input
            id="email"
            type="email"
            name="email"
            value="<?= htmlspecialchars(
                (string) old(
                    'email',
                    $company->email
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>">

        <?php if (error('email')): ?>
            <small class="form-error">
                <?= htmlspecialchars(
                    (string) error('email'),
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
                (string) old(
                    'tax',
                    $company->tax
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>">

        <?php if (error('tax')): ?>
            <small class="form-error">
                <?= htmlspecialchars(
                    (string) error('tax'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </small>
        <?php endif; ?>
    </div>

    <button type="submit">Guardar cambios</button>

    <a href="<?= htmlspecialchars(
        url('empresa'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        Cancelar
    </a>
</form>