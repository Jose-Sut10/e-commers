<h1>
    Inventario:
    <?= htmlspecialchars(
        (string) $product->name,
        ENT_QUOTES,
        'UTF-8'
    ) ?>
</h1>

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

<h2>
    Stock actual:
    <?= (int) $product->stock ?>
</h2>

<?php $success = session('success'); ?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars(
            (string) $success,
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

    <div>
        <label for="type">
            Tipo
        </label>

        <select
            id="type"
            name="type"
        >
            <option value="">
                Seleccionar
            </option>

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
        <label for="quantity">
            Cantidad
        </label>

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
        <label for="reason">
            Motivo
        </label>

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

    <button type="submit">
        Registrar movimiento
    </button>
</form>

<hr>

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