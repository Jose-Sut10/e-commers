<?php
use Core\Auth\Auth;
$user = Auth::user();
?>

<h1>Panel principal</h1>

<?php if ($user): ?>
    <p>
        Bienvenido,
        <strong>
            <?= htmlspecialchars(
                (string) $user->name,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </strong>
    </p>

    <p>
        <?= htmlspecialchars(
            (string) $user->email,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </p>

    <form
        method="POST"
        action="<?= htmlspecialchars(
            url('logout'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>">
        <?= csrf_field() ?>

        <button type="submit">Cerrar sesión</button>
    </form>
<?php else: ?>
    <p>No has iniciado sesión.</p>

    <a href="<?= htmlspecialchars(
        url('login'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        Iniciar sesión
    </a>
<?php endif; ?>