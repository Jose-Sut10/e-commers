<?php
$success = session('success');
$warning = session('warning');

$formatDate = function (
    mixed $value
): string {

    if (!$value) {
        return '';
    }

    $timestamp =
        strtotime(
            (string) $value
        );

    if (!$timestamp) {
        return '';
    }

    return date(
        'Y-m-d\TH:i',
        $timestamp
    );
};
?>

<a href="<?= htmlspecialchars(
    url('productos'),
    ENT_QUOTES,
    'UTF-8'
) ?>">
    ← Volver a productos
</a>

<h1>
    Promociones:
    <?= htmlspecialchars(
        (string) $product->name,
        ENT_QUOTES,
        'UTF-8'
    ) ?>
</h1>

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

<section class="dashboard-section">
    <h2>Promoción general</h2>

    <p>
        Precio normal:

        <strong>
            Q <?= number_format(
                (float) $product->price,
                2
            ) ?>
        </strong>
    </p>

    <?php if ($product->hasActiveSale()): ?>
        <p>
            Precio actual:

            <strong>
                Q <?= number_format(
                    $product->finalPrice(),
                    2
                ) ?>
            </strong>
            —
            <?= $product->discountPercentage() ?>%
            de descuento
        </p>

    <?php endif; ?>

    <form
        method="POST"
        action="<?= htmlspecialchars(
            url('productos/promocion'),
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
            <label for="product_sale_price">Precio de oferta</label>

            <input
                id="product_sale_price"
                type="number"
                name="sale_price"
                min="0"
                step="0.01"

                value="<?= htmlspecialchars(
                    (string) (
                        $product->sale_price
                        ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
        </div>

        <div>
            <label for="product_sale_starts">Inicio de promoción</label>

            <input
                id="product_sale_starts"
                type="datetime-local"
                name="sale_starts_at"

                value="<?= htmlspecialchars(
                    $formatDate(
                        $product->sale_starts_at
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
        </div>

        <div>
            <label for="product_sale_ends"> Fin de promoción </label>

            <input
                id="product_sale_ends"
                type="datetime-local"
                name="sale_ends_at"

                value="<?= htmlspecialchars(
                    $formatDate(
                        $product->sale_ends_at
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
        </div>

        <button type="submit"> Guardar promoción</button>

        <p>
            <small>
                Para quitar la promoción,
                deja vacío el precio de oferta
                y guarda.
            </small>
        </p>
    </form>
</section>

<?php if (!empty($variants)): ?>

<section class="dashboard-section">
    <h2>Promociones por variante</h2>

    <?php foreach ($variants as $variant): ?>
        <div
            style="
                border-bottom:1px solid #eee;
                padding:20px 0;
            "
        >
            <h3>
                <?= htmlspecialchars(
                    (string) $variant->name,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </h3>

            <p>
                SKU:
                <?= htmlspecialchars(
                    (string) $variant->sku,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

            <p>
                Precio normal:
                <strong>

                    Q <?= number_format(
                        $variant->basePrice(
                            $product
                        ),
                        2
                    ) ?>
                </strong>
            </p>

            <?php if (
                $variant->hasActiveSale(
                    $product
                )
            ): ?>
                <p>
                    Precio de oferta activo:
                    <strong>

                        Q <?= number_format(
                            $variant->finalPrice(
                                $product
                            ),
                            2
                        ) ?>
                    </strong>
                </p>
            <?php endif; ?>

            <form
                method="POST"
                action="<?= htmlspecialchars(
                    url(
                        'productos/promocion/variante'
                    ),
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

                <input
                    type="hidden"
                    name="variant_id"
                    value="<?= (int) $variant->id ?>"
                >

                <div>
                    <label>Precio de oferta</label>

                    <input
                        type="number"
                        name="sale_price"
                        min="0"
                        step="0.01"

                        value="<?= htmlspecialchars(
                            (string) (
                                $variant->sale_price
                                ?? ''
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >
                </div>

                <div>
                    <label>Inicio</label>

                    <input
                        type="datetime-local"
                        name="sale_starts_at"

                        value="<?= htmlspecialchars(
                            $formatDate(
                                $variant->sale_starts_at
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >
                </div>

                <div>
                    <label> Fin </label>

                    <input
                        type="datetime-local"
                        name="sale_ends_at"

                        value="<?= htmlspecialchars(
                            $formatDate(
                                $variant->sale_ends_at
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >
                </div>

                <button type="submit"> Guardar promoción</button>
            </form>
        </div>
    <?php endforeach; ?>
</section>
<?php endif; ?>