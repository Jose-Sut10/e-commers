<h1>Cupones</h1>
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
        url('cupones/crear'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        Nuevo cupón
    </a>
</div>

<?php if (empty($coupons)): ?>
    <p>Todavía no hay cupones registrados.</p>
<?php else: ?>

<table>
    <thead>
        <tr>
            <th>Código</th>
            <th>Tipo</th>
            <th>Descuento</th>
            <th>Compra mínima</th>
            <th>Usos</th>
            <th>Vigencia</th>
            <th>Estado</th>
            <th></th>
        </tr>
    </thead>

    <tbody>
    <?php foreach ($coupons as $coupon): ?>
        <tr>
            <td>
                <strong>
                    <?= htmlspecialchars(
                        (string) $coupon->code,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>
            </td>

            <td>
                <?= htmlspecialchars(
                    $coupon->typeLabel(),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </td>

            <td>

                <?php if (
                    $coupon->type
                    === 'percentage'
                ): ?>

                    <?= number_format(
                        (float) $coupon->value,
                        2
                    ) ?>%

                <?php else: ?>

                    Q <?= number_format(
                        (float) $coupon->value,
                        2
                    ) ?>

                <?php endif; ?>

            </td>

            <td>
                Q <?= number_format(
                    (float) $coupon->min_order,
                    2
                ) ?>
            </td>

            <td>
                <?= (int) $coupon->used_count ?>
                /
                <?= $coupon->usage_limit
                    ? (int) $coupon->usage_limit
                    : '∞'
                ?>
            </td>

            <td>

                <?= htmlspecialchars(
                    (string) (
                        $coupon->starts_at
                        ?: 'Inmediato'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
                <br>
                hasta
                <?= htmlspecialchars(
                    (string) (
                        $coupon->ends_at
                        ?: 'Sin vencimiento'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </td>

            <td>
                <?= (bool) $coupon->active
                    ? 'Activo'
                    : 'Inactivo'
                ?>
            </td>

            <td>
                <a href="<?= htmlspecialchars(
                    url(
                        'cupones/editar?id='
                        . (int) $coupon->id
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>">
                    Editar
                </a>
            </td>
        </tr>
    <?php endforeach; ?>

    </tbody>
</table>
<?php endif; ?>