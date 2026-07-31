<aside class="sidebar">

    <div class="logo">

        <h2>EcommerceCMS</h2>

    </div>

    <nav>

        <?php

        $menu = \Core\Config::get('app.menu');

        foreach ($menu as $item):

        ?>

            <a href="<?= $item['route']; ?>">

                <i class="<?= $item['icon']; ?>"></i>

                <span><?= $item['title']; ?></span>

            </a>

        <?php endforeach; ?>

    </nav>

</aside>