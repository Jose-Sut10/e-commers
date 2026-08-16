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
            <label for="variant_id">
                Selecciona una opción
            </label>

            <select
                id="variant_id"
                name="variant_id"
                required
            >

                <option value="">Seleccionar</option>

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
        <label for="quantity">Cantidad</label>

        <input
            id="quantity"
            type="number"
            name="quantity"
            min="1"
            value="1"
            required
        >
    </div>

    <button type="submit">Agregar al carrito</button>

</form>

<?php else: ?>
    <p><strong> Producto agotado</strong></p>
<?php endif; ?>