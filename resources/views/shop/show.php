<?php
$hasVariants = !empty($variants);
$availableStock = $hasVariants
    ? array_sum(
        array_map(
            fn ($variant) =>
                (int) $variant->stock,
            $variants
        )
    )
    : (int) $product->stock;
?>

<!-- =====================================================
     INFORMACIÓN DEL PRODUCTO
===================================================== -->

<h1>
    <?= htmlspecialchars(
        (string) $product->name,
        ENT_QUOTES,
        'UTF-8'
    ) ?>
</h1>

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

<!-- =====================================================
     PRECIO
===================================================== -->

<?php if ($product->hasActiveSale()): ?>
    <div class="product-detail-price">
        <del>
            <?= money($product->price) ?>
        </del>

        <h2><?= money($couponResult['subtotal']) ?></h2>

        <span class="sale-badge">
            <?= $product->discountPercentage() ?>%
            de descuento
        </span>
    </div>

<?php else: ?>

    <div class="product-detail-price">
        <h2>
            Q <?= number_format(
                (float) $product->price,
                2
            ) ?>
        </h2>
    </div>

<?php endif; ?>

<!-- =====================================================
     STOCK
===================================================== -->

<p>
    Existencias disponibles:
    <strong>
        <?= $availableStock ?>
    </strong>
</p>

<!-- =====================================================
     AGREGAR AL CARRITO
===================================================== -->

<?php if ($availableStock > 0): ?>

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

    <?php if ($hasVariants): ?>
        <div>
            <label for="variant_id"> Selecciona una opción</label>

            <select
                id="variant_id"
                name="variant_id"
                required
            >
            
                <option value=""> Seleccionar</option>

                <?php foreach ($variants as $variant): ?>

                    <?php
                    $variantPrice =
                        $variant->finalPrice(
                            $product
                        );
                    ?>

                    <option
                        value="<?= (int) $variant->id ?>"
                        <?= (int) $variant->stock <= 0
                            ? 'disabled'
                            : ''
                        ?>
                    >
                        <?= htmlspecialchars(
                            (string) $variant->name,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                        —
                        Q <?= number_format(
                            $variantPrice,
                            2
                        ) ?>

                        —
                        Stock:
                        <?= (int) $variant->stock ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php endif; ?>

    <div>
        <label for="quantity"> Cantidad</label>

        <input
            id="quantity"
            type="number"
            name="quantity"
            min="1"
            value="1"
            required
        >
    </div>

    <button type="submit"> Agregar al carrito</button>
</form>

<?php else: ?>
    <p>
        <strong>
            Producto agotado
        </strong>
    </p>
<?php endif; ?>