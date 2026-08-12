<h1>
    Variantes:
    <?= htmlspecialchars(
        (string) $product->name,
        ENT_QUOTES,
        'UTF-8'
    ) ?>
</h1>

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

<div class="page-actions">
    <a href="<?= htmlspecialchars(
        url(
            'productos/variantes/crear?id='
            . (int) $product->id
        ),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        Nueva variante
    </a>

    <a href="<?= htmlspecialchars(
        url('productos'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        Volver a productos
    </a>
</div>

<?php if (empty($variants)): ?>
    <p>Este producto todavía no tiene variantes.</p>
<?php else: ?>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Variante</th>
            <th>SKU</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Estado</th>
        </tr>

    </thead>

    <tbody>
        <?php foreach ($variants as $variant): ?>
            <tr>

                <td>
                    <?= (int) $variant->id ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        (string) $variant->name,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        (string) $variant->sku,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </td>

                <td>

                    <?php if (
                        $variant->price !== null
                        && $variant->price !== ''
                    ): ?>

                        Q <?= number_format(
                            (float) $variant->price,
                            2
                        ) ?>

                    <?php else: ?>

                        Precio base:
                        Q <?= number_format(
                            (float) $product->price,
                            2
                        ) ?>

                    <?php endif; ?>

                </td>

                <td>
                    <?= (int) $variant->stock ?>
                </td>

                <td>
                    <?= (bool) $variant->active
                        ? 'Activa'
                        : 'Inactiva'
                    ?>
                </td>

            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>