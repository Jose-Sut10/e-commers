<a href="<?= htmlspecialchars(
    url('tienda'),
    ENT_QUOTES,
    'UTF-8'
) ?>">
    ← Volver a la tienda
</a>

<h1>
    <?= htmlspecialchars(
        (string) $product->name,
        ENT_QUOTES,
        'UTF-8'
    ) ?>
</h1>

<p>
    Categoría:
    <strong>
        <?= htmlspecialchars(
            (string) $product->category_name,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </strong>
</p>

<!-- Galería -->

<?php if (!empty($images)): ?>
    <div class="product-gallery">
        <?php foreach ($images as $image): ?>
            <img
                src="<?= htmlspecialchars(
                    asset(
                        (string) $image->path
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                alt="<?= htmlspecialchars(
                    (string) $product->name,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                width="350"
            >
        <?php endforeach; ?>
    </div>

<?php elseif ($product->image_path): ?>
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
        width="350"
    >

<?php else: ?>
    <p>Producto sin imágenes.</p>
<?php endif; ?>

<h2>
    Q <?= number_format(
        (float) $product->price,
        2
    ) ?>
</h2>

<?php if ($product->description): ?>
    <p>
        <?= nl2br(
            htmlspecialchars(
                (string) $product->description,
                ENT_QUOTES,
                'UTF-8'
            )
        ) ?>
    </p>

<?php endif; ?>

<p>
    SKU:
    <?= htmlspecialchars(
        (string) $product->sku,
        ENT_QUOTES,
        'UTF-8'
    ) ?>
</p>


<?php if ((int) $product->stock > 0): ?>
    <p><strong>Disponible</strong></p>

    <p>
        Existencias:
        <?= (int) $product->stock ?>
    </p>

<?php else: ?>
    <p><strong>Producto agotado</strong></p>
<?php endif; ?>