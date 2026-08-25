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
    <p><strong>Notas:</strong>

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

<p>
    Subtotal:
    <strong>
        Q <?= number_format(
            (float) $order->subtotal,
            2
        ) ?>
    </strong>
</p>

<?php if (
    (float)
    $order->discount_total > 0
): ?>

    <p>
        Cupón:
        <strong>
            <?= htmlspecialchars(
                (string) (
                    $order->coupon_code
                    ?: '-'
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </strong>
    </p>

    <p>
        Descuento:
        <strong>
            - Q <?= number_format(
                (float)
                $order->discount_total,
                2
            ) ?>
        </strong>

    </p>

<?php endif; ?>

<?php if (
    $order->shipping_method_name
): ?>

    <p>
        <strong>Método de envío:</strong>

        <?= htmlspecialchars(
            (string)
            $order->shipping_method_name,
            ENT_QUOTES,
            'UTF-8'
        ) ?>

    </p>

    <p>
        <strong>Envío:</strong>

        Q <?= number_format(
            (float)
            $order->shipping_total,
            2
        ) ?>
    </p>
<?php endif; ?>
<hr>
<h3>Pago</h3>

<p>
    <strong> Método:</strong>
    <?= htmlspecialchars(
        (string) (
            $order->payment_method_name
            ?: 'No especificado'
        ),
        ENT_QUOTES,
        'UTF-8'
    ) ?>
</p>

<p>
    <strong>Estado del pago:</strong>
    <?php if (
        $order->payment_status
        === 'paid'
    ): ?>
        Pagado
    <?php else: ?>
        Pendiente de pago
    <?php endif; ?>
</p>

<?php if ($order->paid_at): ?>
    <p>
        <strong>Fecha de pago:</strong>

        <?= htmlspecialchars(
            (string) $order->paid_at,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </p>
<?php endif; ?>

<form
    method="POST"
    action="<?= htmlspecialchars(
        url('pedidos/pago'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>"
>

    <?= csrf_field() ?>

    <input
        type="hidden"
        name="order_id"
        value="<?= (int) $order->id ?>"
    >

    <?php if (
        $order->payment_status
        === 'paid'
    ): ?>

        <input
            type="hidden"
            name="payment_status"
            value="pending"
        >

        <button type="submit">Marcar pago como pendiente</button>

    <?php else: ?>

        <input
            type="hidden"
            name="payment_status"
            value="paid"
        >

        <button type="submit">Marcar como pagado</button>

    <?php endif; ?>
</form>

<?php if ($order->payment_method_code === 'bank_transfer'): ?>
    <div
        style="
            margin-top:20px;
            padding:20px;
            border:1px solid #e5e7eb;
            border-radius:10px;
        "
    >
        <h3>Comprobante de transferencia</h3>

        <?php if ($order->payment_proof_path): ?>

            <a
                href="<?= htmlspecialchars(
                    asset(
                        (string)
                        $order->payment_proof_path
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                target="_blank"
                rel="noopener noreferrer"
            >
                <img
                    src="<?= htmlspecialchars(
                        asset(
                            (string)
                            $order->payment_proof_path
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    alt="Comprobante de transferencia"
                    style="
                        display:block;
                        width:100%;
                        max-width:500px;
                        max-height:500px;
                        object-fit:contain;
                        margin-top:15px;
                        border-radius:8px;
                        border:1px solid #ddd;
                    "
                >
            </a>

            <?php if ($order->payment_proof_uploaded_at): ?>
                <p>
                    <small>
                        Comprobante recibido:

                        <?= htmlspecialchars(
                            (string)
                            $order->payment_proof_uploaded_at,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </small>
                </p>
            <?php endif; ?>

        <?php else: ?>
            <div class="alert alert-warning">
                Este pedido no tiene comprobante.
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

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


<?php $transitions = $order->allowedTransitions();?>

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

        <label for="status">Nuevo estado</label>

        <select
            id="status"
            name="status"
            required
        >
            <option value="">Seleccionar</option>
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
                        'confirmed' => 'Confirmado',
                        'shipped' => 'Enviado',
                        'delivered' => 'Entregado',
                        'cancelled' => 'Cancelado',
                        default => $status,
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

<?php if ($order->status === 'confirmed'): ?>

<section class="dashboard-section">
    <h2> Despachar pedido</h2>

    <p>
        Registra los datos de la empresa
        transportista y la guía antes de
        marcar el pedido como enviado.
    </p>

    <form
        method="POST"
        enctype="multipart/form-data"
        action="<?= htmlspecialchars(
            url('pedidos/despachar'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >
        <?= csrf_field() ?>

        <input
            type="hidden"
            name="order_id"
            value="<?= (int) $order->id ?>"
        >

        <div>
            <label for="shipping_carrier"> Empresa transportista</label>

            <input
                id="shipping_carrier"
                type="text"
                name="shipping_carrier"
                maxlength="100"
                placeholder="Ej. Cargo Expreso"
                required
            >
        </div>

        <div>
            <label for="tracking_number">Número de guía</label>

            <input
                id="tracking_number"
                type="text"
                name="tracking_number"
                maxlength="150"
                placeholder="Ej. 123456789"
                required
            >
        </div>

        <div>
            <label for="shipping_guide">Imagen de la guía</label>
            <input
                id="shipping_guide"
                type="file"
                name="shipping_guide"
                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                required
            >
            <small>JPG, PNG o WEBP. Máximo 4 MB.</small>
        </div>

        <button type="submit">Despachar pedido</button>
    </form>

</section>

<?php endif; ?>

<?php if (
    $order->shipping_carrier
    || $order->tracking_number
    || $order->shipping_guide_path
): ?>

<section class="dashboard-section">
    <h2>Información del despacho</h2>
    <p>
        <strong>Transportista:</strong>

        <?= htmlspecialchars(
            (string) (
                $order->shipping_carrier
                ?: '-'
            ),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </p>

    <p>
        <strong>Número de guía:</strong>

        <?= htmlspecialchars(
            (string) (
                $order->tracking_number
                ?: '-'
            ),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </p>

    <?php if ($order->shipped_at): ?>
        <p>
            <strong>Fecha de despacho:</strong>

            <?= htmlspecialchars(
                (string)
                $order->shipped_at,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>
    <?php endif; ?>

    <?php if ($order->delivered_at): ?>
        <p>
            <strong>Fecha de entrega:</strong>

            <?= htmlspecialchars(
                (string)
                $order->delivered_at,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>
    <?php endif; ?>

    <?php if ($order->shipping_guide_path): ?>

        <div style="margin-top:20px;">
            <h3>Guía de envío</h3>

            <a
                href="<?= htmlspecialchars(
                    asset(
                        (string)
                        $order->shipping_guide_path
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                target="_blank"
                rel="noopener noreferrer"
            >

                <img
                    src="<?= htmlspecialchars(
                        asset(
                            (string)
                            $order->shipping_guide_path
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    alt="Guía de envío"

                    style="
                        display:block;
                        width:100%;
                        max-width:500px;
                        max-height:500px;
                        object-fit:contain;
                        border:1px solid #ddd;
                        border-radius:8px;
                    "
                >
            </a>
        </div>
    <?php endif; ?>

</section>
<?php endif; ?>

<?php else: ?>
    <p>Este pedido ya no admite cambios de estado.</p>
<?php endif; ?>