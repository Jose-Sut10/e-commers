<?php
$success = session('success');
$warning = session('warning');
?>

<a href="<?= htmlspecialchars(
    url('pedidos'),
    ENT_QUOTES,
    'UTF-8'
) ?>">
    ← Volver a pedidos
</a>

<h1>
    Pedido
    <?= htmlspecialchars(
        (string) $order->number,
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

<h2>Información del cliente</h2>

<p>
    <strong>Nombre:</strong>

    <?= htmlspecialchars(
        (string) $order->customer_name,
        ENT_QUOTES,
        'UTF-8'
    ) ?>
</p>

<p>
    <strong>Teléfono:</strong>

    <?= htmlspecialchars(
        (string) $order->customer_phone,
        ENT_QUOTES,
        'UTF-8'
    ) ?>
</p>

<p>
    <strong>Correo:</strong>

    <?= htmlspecialchars(
        (string) (
            $order->customer_email
            ?: 'No registrado'
        ),
        ENT_QUOTES,
        'UTF-8'
    ) ?>
</p>

<p>
    <strong>Dirección:</strong>

    <?= nl2br(
        htmlspecialchars(
            (string) $order->customer_address,
            ENT_QUOTES,
            'UTF-8'
        )
    ) ?>
</p>

<?php if ($order->notes): ?>

    <p>
        <strong>Notas:</strong>

        <?= nl2br(
            htmlspecialchars(
                (string) $order->notes,
                ENT_QUOTES,
                'UTF-8'
            )
        ) ?>
    </p>

<?php endif; ?>

<hr>

<h2>Productos</h2>

<table>
    <thead>
        <tr>
            <th>Producto</th>
            <th>SKU</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
            <th>Variante</th>
        </tr>
    </thead>

    <tbody>

        <?php foreach ($items as $item): ?>

            <tr>

                <td>
                    <?= htmlspecialchars(
                        (string) $item->product_name,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        (string) $item->sku,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </td>

                <td>
                    Q <?= number_format(
                        (float) $item->unit_price,
                        2
                    ) ?>
                </td>

                <td>
                    <?= (int) $item->quantity ?>
                </td>

                <td>
                    Q <?= number_format(
                        (float) $item->subtotal,
                        2
                    ) ?>
                </td>

            </tr>

            <td>
                <?= htmlspecialchars(
                    (string) (
                        $item->variant_name
                        ?: 'Producto base'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </td>

        <?php endforeach; ?>

    </tbody>
</table>

<h2>
    Total:
    Q <?= number_format(
        (float) $order->total,
        2
    ) ?>
</h2>


<hr>

<h2>Estado del pedido</h2>

<p>
    Estado actual:

    <strong>
        <?= htmlspecialchars(
            $order->statusLabel(),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </strong>
</p>


<?php
$transitions =
    $order->allowedTransitions();
?>

<?php if (!empty($transitions)): ?>

    <form
        method="POST"
        action="<?= htmlspecialchars(
            url('pedidos/estado'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >
        <?= csrf_field() ?>

        <input
            type="hidden"
            name="id"
            value="<?= (int) $order->id ?>"
        >

        <label for="status">
            Nuevo estado
        </label>

        <select
            id="status"
            name="status"
            required
        >

            <option value="">
                Seleccionar
            </option>

            <?php foreach ($transitions as $status): ?>

                <option
                    value="<?= htmlspecialchars(
                        $status,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >
                    <?php
                    echo match ($status) {
                        'confirmed' =>
                            'Confirmado',

                        'shipped' =>
                            'Enviado',

                        'delivered' =>
                            'Entregado',

                        'cancelled' =>
                            'Cancelado',

                        default =>
                            $status,
                    };
                    ?>
                </option>

            <?php endforeach; ?>

        </select>

        <button
            type="submit"
            onclick="return confirm(
                '¿Confirmas el cambio de estado?'
            );"
        >
            Actualizar estado
        </button>

    </form>

<?php else: ?>
    <p>Este pedido ya no admite cambios de estado.</p>
<?php endif; ?>