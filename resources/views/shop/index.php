<h1>
    <?= htmlspecialchars(
        (string) (
            $company?->name
            ?: 'Tienda'
        ),
        ENT_QUOTES,
        'UTF-8'
    ) ?>
</h1>

<h2>Productos</h2>

<!-- Categorías -->

<nav class="shop-categories">
    <a href="<?= htmlspecialchars(
        url('tienda'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        Todos
    </a>

    <?php foreach ($categories as $category): ?>

        <a href="<?= htmlspecialchars(
            url(
                'tienda?categoria='
                . urlencode(
                    (string) $category->slug
                )
            ),
            ENT_QUOTES,
            'UTF-8'
        ) ?>">
            <?= htmlspecialchars(
                (string) $category->name,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </a>
    <?php endforeach; ?>
</nav>

<hr>

<?php if (empty($products)): ?>

    <p>No hay productos disponibles.</p>

<?php else: ?>

    <div class="product-grid">

        <?php foreach ($products as $product): ?>

            <article class="product-card">

                <a href="<?= htmlspecialchars(
                    url(
                        'producto?slug='
                        . urlencode(
                            (string) $product->slug
                        )
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>">

                    <?php if ($product->image_path): ?>

                        <img
                            src="<?= htmlspecialchars(
                                asset(
                                    (string) $product->image_path
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            alt="<?= htmlspecialchars(
                                (string) $product->name,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            width="250"
                        >

                    <?php else: ?>

                        <div class="product-no-image">
                            Sin imagen
                        </div>

                    <?php endif; ?>

                </a>

                <h3>
                    <a href="<?= htmlspecialchars(
                        url(
                            'producto?slug='
                            . urlencode(
                                (string) $product->slug
                            )
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">
                        <?= htmlspecialchars(
                            (string) $product->name,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </a>
                </h3>

                <p>
                    <?= htmlspecialchars(
                        (string) $product->category_name,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

                <p>
                    <strong>
                        Q <?= number_format(
                            (float) $product->price,
                            2
                        ) ?>
                    </strong>
                </p>


                <?php if ((int) $product->stock > 0): ?>
                    <p>Disponible</p>
                <?php else: ?>
                    <p>Agotado</p>
                <?php endif; ?>


                <a href="<?= htmlspecialchars(
                    url(
                        'producto?slug='
                        . urlencode(
                            (string) $product->slug
                        )
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>">
                    Ver producto
                </a>

            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>