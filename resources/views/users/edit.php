<h1>Editar usuario</h1>
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
        url('usuarios/actualizar'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
    <?= csrf_field() ?>

    <input
        type="hidden"
        name="id"
        value="<?= (int) $user->id ?>">

    <div>
        <label for="name">Nombre</label>

        <input
            id="name"
            type="text"
            name="name"
            value="<?= htmlspecialchars(
                (string) old('name', $user->name),
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
                (string) old('email', $user->email),
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
        <label for="password">Nueva contraseña</label>

        <input
            id="password"
            type="password"
            name="password"
            autocomplete="new-password">

        <small>Déjala vacía para conservar la contraseña actual.</small>

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
        <label for="password_confirmation">
            Confirmar nueva contraseña
        </label>

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
        <?php
        $selectedRole = old(
            'role',
            $user->role
        );
        ?>

        <select
            id="role"
            name="role">
            <option
                value="user"
                <?= $selectedRole === 'user'
                    ? 'selected'
                    : ''
                ?>>
                Usuario
            </option>

            <option
                value="admin"
                <?= $selectedRole === 'admin'
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
        <?php
        $activeValue = old(
            'active',
            (bool) $user->active ? '1' : '0'
        );
        ?>

        <label>
            <input
                type="checkbox"
                name="active"
                value="1"
                <?= $activeValue === '1'
                    ? 'checked'
                    : ''
                ?>>
            Usuario activo
        </label>

        <?php if (error('active')): ?>
            <small class="form-error">
                <?= htmlspecialchars(
                    (string) error('active'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </small>
        <?php endif; ?>
    </div>

    <button type="submit">Guardar cambios</button>

    <a href="<?= htmlspecialchars(
        url('usuarios'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        Cancelar
    </a>
</form>