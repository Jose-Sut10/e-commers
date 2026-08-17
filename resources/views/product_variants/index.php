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
        url(
            'inventario?id='
            . (int) $product->id
        ),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        Inventario
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
                <th>Variante</th>
                <th>SKU</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($variants as $variant): ?>
                <tr>
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

                            Q <?= number_format(
                                (float) $product->price,
                                2
                            ) ?>

                            <small>(precio base) </small>

                        <?php endif; ?>
                    </td>

                    <td>
                        <strong> <?= (int) $variant->stock ?></strong>
                    </td>

                    <td>
                        <?php if ((bool) $variant->active): ?>
                            <span> Activa</span>
                        <?php else: ?>
                            <span> Inactiva</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <a href="<?= htmlspecialchars(
                            url(
                                'productos/variantes/editar?id='
                                . (int) $product->id
                                . '&variant='
                                . (int) $variant->id
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>">
                            Editar
                        </a>

                        <form
                            method="POST"
                            action="<?= htmlspecialchars(
                                url(
                                    'productos/variantes/eliminar'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            style="display:inline;"
                            onsubmit="return confirm(
                                '¿Seguro que deseas eliminar esta variante?'
                            );"
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

                            <button type="submit">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>

        </tbody>
    </table>
<?php endif; ?>