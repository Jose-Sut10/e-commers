<?php
$success = session('success');
$warning = session('warning');
?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars(
            (string) $success,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </div>
<?php endif; ?>

<?php if ($warning): ?>
    <div class="alert alert-warning">
        <?= htmlspecialchars(
            (string) $warning,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </div>
<?php endif; ?>

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

<?php if ((int) $product->stock > 0): ?>

    <form
        method="POST"
        action="<?= htmlspecialchars(
            url('carrito/agregar'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >
        <?= csrf_field() ?>

        <input
            type="hidden"
            name="product_id"
            value="<?= (int) $product->id ?>"
        >

        <div>
            <label for="quantity">
                Cantidad
            </label>

            <input
                id="quantity"
                type="number"
                name="quantity"
                min="1"
                max="<?= (int) $product->stock ?>"
                value="1"
            >
        </div>

        <button type="submit">
            Agregar al carrito
        </button>
    </form>

<?php endif; ?>