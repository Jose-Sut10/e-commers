<h1>
    Inventario:
    <?= htmlspecialchars(
        (string) $product->name,
        ENT_QUOTES,
        'UTF-8'
    ) ?>
</h1>

<?php
$success = session('success');
$warning = session('warning');

$hasVariants =
    !empty($variants);
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

<?php if (error('general')): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars(
            (string) error('general'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </div>

<?php endif; ?>

<p>
    SKU:
    <strong>
        <?= htmlspecialchars(
            (string) $product->sku,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </strong>
</p>

<!-- =====================================================
     STOCK
===================================================== -->

<?php if ($hasVariants): ?>
    <?php
    $totalStock = 0;

    foreach ($variants as $variant) {
        $totalStock +=
            (int) $variant->stock;
    }
    ?>

    <h2>
        Stock total:
        <?= $totalStock ?>
    </h2>

    <table>
        <thead>

            <tr>
                <th>Variante</th>
                <th>SKU</th>
                <th>Stock</th>
                <th>Estado</th>
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
                        <strong>
                            <?= (int) $variant->stock ?>
                        </strong>
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

<?php else: ?>
    <h2>
        Stock actual:
        <?= (int) $product->stock ?>
    </h2>
<?php endif; ?>

<hr>

<!-- =====================================================
     NUEVO MOVIMIENTO
===================================================== -->
<h2>Registrar movimiento</h2>

<form
    method="POST"
    action="<?= htmlspecialchars(
        url('inventario/movimiento'),
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
            <label for="variant_id">Variante</label>

            <select
                id="variant_id"
                name="variant_id"
            >

                <option value="">Selecciona una variante</option>

                <?php foreach ($variants as $variant): ?>
                    <option
                        value="<?= (int) $variant->id ?>"
                        <?= (string) old(
                            'variant_id'
                        ) === (string) $variant->id
                            ? 'selected'
                            : ''
                        ?>
                    >

                        <?= htmlspecialchars(
                            (string) $variant->name,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                        —
                        Stock:
                        <?= (int) $variant->stock ?>

                    </option>
                <?php endforeach; ?>
            </select>

            <?php if (error('variant_id')): ?>
                <small class="form-error">
                    <?= htmlspecialchars(
                        (string) error(
                            'variant_id'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </small>
            <?php endif; ?>
        </div>

    <?php endif; ?>

    <div>

        <label for="type">Tipo</label>

        <select id="type"name="type">
            <option value="">Seleccionar</option>

            <option
                value="in"
                <?= old('type') === 'in'
                    ? 'selected'
                    : ''
                ?>
            >
                Entrada
            </option>

            <option
                value="out"
                <?= old('type') === 'out'
                    ? 'selected'
                    : ''
                ?>
            >
                Salida
            </option>
        </select>

        <?php if (error('type')): ?>
            <small class="form-error">
                <?= htmlspecialchars(
                    (string) error('type'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </small>
        <?php endif; ?>
    </div>

    <div>
        <label for="quantity">Cantidad</label>

        <input
            id="quantity"
            type="number"
            name="quantity"
            min="1"
            step="1"
            value="<?= htmlspecialchars(
                (string) old('quantity'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <?php if (error('quantity')): ?>
            <small class="form-error">
                <?= htmlspecialchars(
                    (string) error('quantity'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </small>
        <?php endif; ?>
    </div>

    <div>
        <label for="reason">Motivo</label>

        <input
            id="reason"
            type="text"
            name="reason"
            maxlength="255"
            value="<?= htmlspecialchars(
                (string) old('reason'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >
    </div>

    <button type="submit">Registrar movimiento</button>
</form>

<hr>

<!-- =====================================================
     HISTORIAL
===================================================== -->

<h2>Historial</h2>

<?php if (empty($movements)): ?>
    <p>
        Este producto todavía no tiene
        movimientos de inventario.
    </p>
<?php else: ?>

<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Variante</th>
            <th>Tipo</th>
            <th>Cantidad</th>
            <th>Anterior</th>
            <th>Nuevo</th>
            <th>Motivo</th>
            <th>Usuario</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($movements as $movement): ?>
            <tr>
                <td>
                    <?= htmlspecialchars(
                        (string) $movement->created_at,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </td>

                <td>
                    <?php if ($movement->variant_name): ?>

                        <?= htmlspecialchars(
                            (string) $movement->variant_name,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                        <small>
                            <?= htmlspecialchars(
                                (string) $movement->variant_sku,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </small>

                    <?php else: ?>
                        Producto base
                    <?php endif; ?>
                </td>

                <td>
                    <?= $movement->type === 'in'
                        ? 'Entrada'
                        : 'Salida'
                    ?>
                </td>

                <td>
                    <?= (int) $movement->quantity ?>
                </td>

                <td>
                    <?= (int) $movement->previous_stock ?>
                </td>

                <td>
                    <?= (int) $movement->new_stock ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        (string) (
                            $movement->reason
                            ?: '-'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        (string) (
                            $movement->user_name
                            ?: 'Sistema'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php endif; ?>

<p>
    <a href="<?= htmlspecialchars(
        url('productos'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        Volver a productos
    </a>
</p>