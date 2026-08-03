<h1>Registrar usuario</h1>

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
        url('usuarios'),
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
            autocomplete="name"
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
            autocomplete="new-password">

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

    <div>
        <label for="password_confirmation">Confirmar contraseña</label>

        <input
            id="password_confirmation"
            type="password"
            name="password_confirmation"
            autocomplete="new-password">

        <?php if (error('password_confirmation')): ?>
            <small class="form-error">
                <?= htmlspecialchars(
                    (string) error(
                        'password_confirmation'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </small>
        <?php endif; ?>
    </div>

    <div>
        <label for="role">Rol</label>

        <select
            id="role"
            name="role">
            <option
                value="user"
                <?= old('role', 'user') === 'user'
                    ? 'selected'
                    : ''
                ?>>
                Usuario
            </option>

            <option
                value="admin"
                <?= old('role') === 'admin'
                    ? 'selected'
                    : ''
                ?>>
                Administrador
            </option>
        </select>

        <?php if (error('role')): ?>
            <small class="form-error">
                <?= htmlspecialchars(
                    (string) error('role'),
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
            Usuario activo
        </label>
    </div>

    <button type="submit">Guardar usuario</button>

    <a href="<?= htmlspecialchars(
        url('usuarios'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        Cancelar
    </a>
</form>