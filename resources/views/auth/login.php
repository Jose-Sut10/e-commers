<h1>Iniciar sesión</h1>
<?php $success = session('success'); ?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars(
            (string) $success,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </div>
<?php endif; ?>

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
        url('login'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
    <?= csrf_field() ?>

    <div>
        <label for="email">Correo electrónico</label>

        <input
            id="email"
            type="email"
            name="email"
            autocomplete="email"
            value="<?= htmlspecialchars(
                (string) old('email'),
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
        <label for="password">Contraseña</label>

        <input
            id="password"
            type="password"
            name="password"
            autocomplete="current-password">

        <?php if (error('password')): ?>
            <small class="form-error">
                <?= htmlspecialchars(
                    (string) error('password'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </small>
        <?php endif; ?>
    </div>

    <button type="submit">Iniciar sesión</button>
</form>