<?php
use Core\Auth\Auth;
$user = Auth::user();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>
        <?= htmlspecialchars(
            (string) (
                $title
                ?? config('app.name')
            ),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </title>

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            asset(
                'assets/css/admin.css'
            ),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >
</head>

<body>

<div class="admin-app">

    <?php
    require BASE_PATH
        . '/resources/views/partials/sidebar.php';
    ?>


    <div class="admin-main">
        <header class="admin-header">
            <div>
                <button
                    type="button"
                    class="sidebar-toggle"
                    id="sidebar-toggle"
                    aria-label="Abrir menú"
                >
                    ☰
                </button>
            </div>

            <div class="admin-user">
                <?php if ($user): ?>
                    <div class="admin-user-info">

                        <strong>
                            <?= htmlspecialchars(
                                (string) $user->name,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>

                        <small>
                            <?= $user->isAdmin()
                                ? 'Administrador'
                                : 'Usuario'
                            ?>
                        </small>

                    </div>

                    <form
                        method="POST"
                        action="<?= htmlspecialchars(
                            url('logout'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                        <?= csrf_field() ?>

                        <button
                            type="submit"
                            class="logout-button"
                        >
                            Salir
                        </button>

                    </form>
                <?php endif; ?>
            </div>
        </header>

        <main class="admin-content">
            <?= $content ?>
        </main>

        <footer class="admin-footer">

            <?= htmlspecialchars(
                (string) config(
                    'app.name'
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>

            &copy;
            <?= date('Y') ?>
        </footer>
    </div>
</div>

<script>
    const button =
        document.getElementById(
            'sidebar-toggle'
        );

    const sidebar =
        document.querySelector(
            '.admin-sidebar'
        );

    if (button && sidebar) {

        button.addEventListener(
            'click',
            function () {

                sidebar.classList.toggle(
                    'is-open'
                );

            }
        );

    }
</script>
</body>
</html>