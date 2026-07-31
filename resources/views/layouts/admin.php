<!DOCTYPE html>
<html lang="es">

<?php require BASE_PATH . '/resources/views/partials/head.php'; ?>

<body>

<div class="app">

    <?php require BASE_PATH . '/resources/views/partials/sidebar.php'; ?>

    <div class="main">

        <?php require BASE_PATH . '/resources/views/partials/navbar.php'; ?>

        <main class="content">

            <?php require $content; ?>

        </main>

        <?php require BASE_PATH . '/resources/views/partials/footer.php'; ?>

    </div>

</div>

<?php require BASE_PATH . '/resources/views/partials/scripts.php'; ?>

</body>

</html>