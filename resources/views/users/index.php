<h1>Usuarios</h1>
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

<div class="page-actions">
    <a href="<?= htmlspecialchars(
        url('usuarios/crear'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        Registrar usuario
    </a>
</div>

<?php if (empty($users)): ?>

    <p>No hay usuarios registrados.</p>

<?php else: ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Fecha de registro</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <?= (int) $user->id ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            (string) $user->name,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            (string) $user->email,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        <?= $user->isAdmin()
                            ? 'Administrador'
                            : 'Usuario'
                        ?>
                    </td>

                    <td>
                        <?= (bool) $user->active
                            ? 'Activo'
                            : 'Inactivo'
                        ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            (string) $user->created_at,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>