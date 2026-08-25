<?php
use App\Services\CartService;

$companyName =
    isset($company)
    && $company
    && !empty($company->name)
        ? (string) $company->name
        : 'Tienda';
try {
    $cartCount = ( new CartService())->count();

} catch (Throwable $exception) {
    $cartCount = 0;
}

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
            isset($title)
                ? (string) $title
                : $companyName,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </title>

    <!-- =====================================================
         ESTILOS GENERALES
    ====================================================== -->

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            asset('assets/css/reset.css'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            asset('assets/css/variables.css'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            asset('assets/css/layout.css'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            asset('assets/css/navbar.css'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            asset('assets/css/buttons.css'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            asset('assets/css/cards.css'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            asset('assets/css/app.css'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >

</head>


<body>


<!-- =====================================================
     ENCABEZADO DE LA TIENDA
===================================================== -->

<header class="shop-header">
    <div class="shop-header-container">

        <!-- MARCA -->

        <a
            href="<?= htmlspecialchars(
                url('tienda'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="shop-brand"
        >

            <?= htmlspecialchars(
                $companyName,
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </a>


        <!-- NAVEGACIÓN -->

        <nav class="shop-navigation">


            <a
                href="<?= htmlspecialchars(
                    url('tienda'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
                Tienda
            </a>

            <a
                href="<?= htmlspecialchars(
                    url('seguimiento'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
                Seguimiento
            </a>

            <a
                href="<?= htmlspecialchars(
                    url('carrito'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                class="shop-cart-link"
            >

                Carrito

                <?php if ($cartCount > 0): ?>

                    <span class="cart-count">
                        <?= (int) $cartCount ?>
                    </span>

                <?php endif; ?>

            </a>
        </nav>
    </div>
</header>

<!-- =====================================================
     CONTENIDO
===================================================== -->

<main class="shop-main">
    <div class="shop-container">
        <?= $content ?>
    </div>
</main>


<!-- =====================================================
     PIE DE PÁGINA
===================================================== -->

<footer class="shop-footer">
    <div class="shop-container">
        <p>

            &copy;
            <?= date('Y') ?>

            <?= htmlspecialchars(
                $companyName,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>
    </div>
</footer>

</body>

</html>