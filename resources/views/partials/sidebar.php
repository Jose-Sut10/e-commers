<?php
use Core\Auth\Auth;
$user = Auth::user();
$menu = config(
    'app.menu',
    []
);
?>

<aside class="admin-sidebar">
    <div class="sidebar-brand">
        <a href="<?= htmlspecialchars(
            url(),
            ENT_QUOTES,
            'UTF-8'
        ) ?>">
            <span class="sidebar-brand-mark">E</span>

            <span class="sidebar-brand-name">
                <?= htmlspecialchars(
                    (string) config(
                        'app.name',
                        'EcommerceCMS'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </span>
        </a>
    </div>

    <nav class="sidebar-nav">
        <?php foreach ($menu as $item): ?>
            <?php
            if (
                ($item['admin'] ?? false)
                && (
                    !$user
                    || !$user->isAdmin()
                )
            ) {
                continue;
            }

            $route = $item['route'];

            $active =
                route_active($route);
            ?>

            <a
                href="<?= htmlspecialchars(
                    url(
                        $route === '/'
                            ? ''
                            : ltrim(
                                $route,
                                '/'
                            )
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                class="sidebar-link <?= $active
                    ? 'is-active'
                    : ''
                ?>"
            >
                <span class="sidebar-icon">
                    <?= htmlspecialchars(
                        (string) (
                            $item['icon']
                            ?? '•'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </span>

                <span>
                    <?= htmlspecialchars(
                        (string) $item['label'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </span>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-footer">
        <a
            href="<?= htmlspecialchars(
                url('tienda'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            target="_blank"
            class="sidebar-store-link"
        >
            Ver tienda pública ↗
        </a>
    </div>
</aside>