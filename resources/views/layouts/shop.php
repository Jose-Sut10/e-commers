<?php
use App\Services\CartService;

$settings = store_settings();

$businessName =
    trim(
        (string) (
            $settings->business_name
            ?? ''
        )
    );

if ($businessName === '') {
    $businessName = 'Tienda';
}

try {
    $cartCount =(new CartService())->count();
} catch (Throwable $exception) {
    $cartCount = 0;
}

$whatsapp =
    preg_replace(
        '/\D+/',
        '',
        (string) (
            $settings->whatsapp
            ?? ''
        )
    );

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
                    . ' | '
                    . $businessName
                : $businessName,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </title>

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

<header class="shop-header">
    <div class="shop-header-container">
        <a
            href="<?= htmlspecialchars(
                url('tienda'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="shop-brand"
        >
            <?php if (
                $settings->logo_path
            ): ?>

                <img
                    src="<?= htmlspecialchars(
                        asset(
                            (string)
                            $settings->logo_path
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    alt="<?= htmlspecialchars(
                        $businessName,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    class="shop-logo"
                >
            <?php else: ?>
                <?= htmlspecialchars(
                    $businessName,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            <?php endif; ?>
        </a>

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
                    <span class="cart-count"><?= (int) $cartCount ?></span>
                <?php endif; ?>
            </a>
        </nav>
    </div>
</header>

<main class="shop-main">
    <div class="shop-container">
        <?= $content ?>
    </div>
</main>

<footer class="shop-footer">
    <div class="shop-container">
        <div class="shop-footer-grid">
            <!-- TIENDA -->
            <div>
                <h3>
                    <?= htmlspecialchars(
                        $businessName,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </h3>

                <?php if ($settings->address): ?>

                    <p>
                        <?= nl2br(
                            htmlspecialchars(
                                (string)
                                $settings->address,
                                ENT_QUOTES,
                                'UTF-8'
                            )
                        ) ?>
                    </p>

                <?php endif; ?>

                <?php if ($settings->business_hours): ?>
                    <p>
                        <?= nl2br(
                            htmlspecialchars(
                                (string)
                                $settings->business_hours,
                                ENT_QUOTES,
                                'UTF-8'
                            )
                        ) ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- CONTACTO -->
            <div>
                <h3>Contacto</h3>


                <?php if ($settings->phone): ?>
                    <p>
                        Teléfono:
                        <?= htmlspecialchars(
                            (string)
                            $settings->phone,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>
                <?php endif; ?>

                <?php if ($settings->email): ?>
                    <p>
                        <a
                            href="mailto:<?= htmlspecialchars(
                                (string)
                                $settings->email,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >

                            <?= htmlspecialchars(
                                (string)
                                $settings->email,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </a>
                    </p>
                <?php endif; ?>

                <?php if ($whatsapp): ?>
                    <p>
                        <a
                            href="https://wa.me/<?= htmlspecialchars(
                                $whatsapp,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            WhatsApp
                        </a>
                    </p>
                <?php endif; ?>
            </div>

            <!-- REDES -->
            <?php if (
                $settings->facebook_url
                || $settings->instagram_url
                || $settings->tiktok_url
            ): ?>

                <div>
                    <h3>Síguenos</h3>

                    <?php if ($settings->facebook_url): ?>

                        <p>
                            <a
                                href="<?= htmlspecialchars(
                                    (string)
                                    $settings->facebook_url,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                Facebook
                            </a>
                        </p>
                    <?php endif; ?>

                    <?php if ($settings->instagram_url): ?>

                        <p>
                            <a
                                href="<?= htmlspecialchars(
                                    (string)
                                    $settings->instagram_url,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                Instagram
                            </a>
                        </p>
                    <?php endif; ?>

                    <?php if ($settings->tiktok_url): ?>

                        <p>
                            <a
                                href="<?= htmlspecialchars(
                                    (string)
                                    $settings->tiktok_url,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                TikTok
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="shop-footer-bottom">
            &copy;
            <?= date('Y') ?>
            <?= htmlspecialchars(
                $businessName,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </div>
    </div>
</footer>

</body>
</html>