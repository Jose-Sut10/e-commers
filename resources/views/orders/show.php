<?php
$success = session('success');
$warning = session('warning');
$transitions = $order->allowedTransitions();

$manualTransitions =
    array_values(
        array_filter(
            $transitions,
            fn (string $status): bool =>
                $status !== 'shipped'
        )
    );


/*Etiquetas de estados.*/

$statusLabels = [
    'pending' => 'Pendiente',
    'confirmed' => 'Confirmado',
    'shipped' => 'Enviado',
    'delivered' => 'Entregado',
    'cancelled' => 'Cancelado',
];

?>

<!-- =====================================================
     VOLVER
===================================================== -->
<a
    href="<?= htmlspecialchars(
        url('pedidos'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>"
>
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

<!-- =====================================================
     MENSAJES
===================================================== -->
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

<!-- =====================================================
     INFORMACIÓN DEL CLIENTE
===================================================== -->

<section class="dashboard-section">
    <h2> Información del cliente</h2>
    <p>
        <strong>Nombre:</strong>
        <?= htmlspecialchars(
            (string) $order->customer_name,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </p>

    <p>
        <strong> Teléfono: </strong>

        <?= htmlspecialchars(
            (string) $order->customer_phone,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </p>

    <p>
        <strong> Correo:</strong>

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

</section>

<!-- =====================================================
     PRODUCTOS
===================================================== -->

<section class="dashboard-section">
    <h2>Productos</h2>

    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Variante</th>
                    <th>SKU</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>

                    <!-- PRODUCTO -->
                    <td>
                        <?= htmlspecialchars(
                            (string) $item->product_name,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </td>

                    <!-- VARIANTE -->

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

                    <!-- SKU -->
                    <td>
                        <?= htmlspecialchars(
                            (string) $item->sku,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <!-- PRECIO -->
                    <td>
                        Q <?= number_format(
                            (float) $item->unit_price,
                            2
                        ) ?>

                    </td>

                    <!-- CANTIDAD -->
                    <td><?= (int) $item->quantity ?></td>

                    <!-- SUBTOTAL -->

                    <td>
                        Q <?= number_format(
                            (float) $item->subtotal,
                            2
                        ) ?>

                    </td>
                </tr>

            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- =====================================================
     RESUMEN ECONÓMICO
===================================================== -->

<section class="dashboard-section">
    <h2>Resumen del pedido</h2>
    <p>
        <strong>Subtotal:</strong>

        Q <?= number_format(
            (float) $order->subtotal,
            2
        ) ?>
    </p>

    <!-- CUPÓN -->
    <?php if (
        (float) $order->discount_total > 0
    ): ?>
        <p>
            <strong>Cupón:</strong>

            <?= htmlspecialchars(
                (string) (
                    $order->coupon_code
                    ?: '-'
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>

        <p>
            <strong>Descuento:</strong>
            - Q <?= number_format(
                (float) $order->discount_total,
                2
            ) ?>
        </p>
    <?php endif; ?>

    <!-- ENVÍO -->
    <?php if ($order->shipping_method_name): ?>
        <p>
            <strong> Método de envío:</strong>

            <?= htmlspecialchars(
                (string) $order->shipping_method_name,
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </p>

        <p>
            <strong>Costo de envío:</strong>
            Q <?= number_format(
                (float) $order->shipping_total,
                2
            ) ?>
        </p>
    <?php endif; ?>

    <hr>

    <h2>
        Total:
        Q <?= number_format(
            (float) $order->total,
            2
        ) ?>
    </h2>
</section>

<!-- =====================================================
     INFORMACIÓN DE PAGO
===================================================== -->
<section class="dashboard-section">
    <h2>Pago</h2>
    <p>
        <strong>Método:</strong>

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
            <span>Pagado</span>

        <?php else: ?>
            <span>Pendiente de pago</span>
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

    <!-- =================================================
         COMPROBANTE DE TRANSFERENCIA
    ================================================== -->

    <?php if (
        $order->payment_method_code
        === 'bank_transfer'
    ): ?>
        <div
            style="
                margin-top:20px;
                padding:20px;
                border:1px solid #e5e7eb;
                border-radius:10px;
            "
        >

            <h3>Comprobante de transferencia</h3>

            <?php if (
                $order->payment_proof_path
            ): ?>

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
                    Este pedido no tiene
                    comprobante de transferencia.
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- =================================================
         CAMBIAR ESTADO DEL PAGO
    ================================================== -->

    <?php if ($order->payment_status === 'paid'): ?>

        <form
            method="POST"
            action="<?= htmlspecialchars(
                url('pedidos/pago'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            style="margin-top:20px;"
        >

            <?= csrf_field() ?>

            <input
                type="hidden"
                name="order_id"
                value="<?= (int) $order->id ?>"
            >

            <input
                type="hidden"
                name="payment_status"
                value="pending"
            >

            <button type="submit">Marcar pago como pendiente</button>
        </form>

    <?php elseif ($order->status !== 'cancelled'): ?>

        <form
            method="POST"
            action="<?= htmlspecialchars(
                url('pedidos/pago'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            style="margin-top:20px;"
        >
            <?= csrf_field() ?>

            <input
                type="hidden"
                name="order_id"
                value="<?= (int) $order->id ?>"
            >

            <input
                type="hidden"
                name="payment_status"
                value="paid"
            >

            <button
                type="submit"
                onclick="return confirm(
                    '¿Confirmas que este pedido ya fue pagado?'
                );"
            >
                Marcar como pagado
            </button>
        </form>
    <?php endif; ?>
</section>

<!-- =====================================================
     ESTADO DEL PEDIDO
===================================================== -->

<section class="dashboard-section">
    <h2>Estado del pedido</h2>

    <p>
        <strong>Estado actual:</strong>

        <?= htmlspecialchars(
            $order->statusLabel(),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </p>

    <!-- =================================================
         CAMBIOS MANUALES
    ================================================== -->
    <?php if (!empty($manualTransitions)): ?>

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

            <div>
                <label for="status">Nuevo estado</label>

                <select
                    id="status"
                    name="status"
                    required
                >
                    <option value="">Seleccionar</option>

                    <?php foreach ($manualTransitions as $status): ?>

                        <option
                            value="<?= htmlspecialchars(
                                $status,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >
                            <?= htmlspecialchars(
                                $statusLabels[
                                    $status
                                ] ?? $status,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button
                type="submit"
                onclick="return confirm(
                    '¿Confirmas el cambio de estado?'
                );"
            >
                Actualizar estado
            </button>
        </form>

    <?php elseif ($order->status !== 'confirmed'): ?>

        <p>
            Este pedido ya no admite
            cambios manuales de estado.
        </p>
    <?php endif; ?>
</section>

<!-- =====================================================
     DESPACHAR PEDIDO
===================================================== -->

<?php if ($order->status === 'confirmed'): ?>

    <section class="dashboard-section">
        <h2>Despachar pedido</h2>

        <p>
            Registra la empresa transportista,
            el número de guía y la imagen de la
            guía antes de marcar el pedido como
            enviado.
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

            <!-- TRANSPORTISTA -->
            <div>
                <label for="shipping_carrier">Empresa transportista</label>

                <input
                    id="shipping_carrier"
                    type="text"
                    name="shipping_carrier"
                    maxlength="100"
                    placeholder="Ej. Cargo Expreso"
                    required
                >
            </div>

            <!-- GUÍA -->
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

            <!-- IMAGEN -->
            <div>
                <label for="shipping_guide">Imagen de la guía</label>

                <input
                    id="shipping_guide"
                    type="file"
                    name="shipping_guide"

                    accept="
                        .jpg,
                        .jpeg,
                        .png,
                        .webp,
                        image/jpeg,
                        image/png,
                        image/webp
                    "
                    required
                >

                <small>
                    JPG, PNG o WEBP.
                    Máximo 4 MB.
                </small>
            </div>

            <button
                type="submit"
                onclick="return confirm(
                    '¿Confirmas que deseas despachar este pedido?'
                );"
            >
                Despachar pedido
            </button>
        </form>
    </section>
<?php endif; ?>

<!-- =====================================================
     INFORMACIÓN DEL DESPACHO
===================================================== -->

<?php if (
    $order->shipping_carrier
    || $order->tracking_number
    || $order->shipping_guide_path
    || $order->shipped_at
    || $order->delivered_at
): ?>
    <section class="dashboard-section">
        <h2>Información del despacho</h2>

        <!-- TRANSPORTISTA -->
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

        <!-- NÚMERO DE GUÍA -->
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

        <!-- FECHA DESPACHO -->
        <?php if ($order->shipped_at): ?>

            <p>
                <strong>Fecha de despacho:</strong>

                <?= htmlspecialchars(
                    (string) $order->shipped_at,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>
        <?php endif; ?>

        <!-- FECHA ENTREGA -->

        <?php if ($order->delivered_at): ?>

            <p>
                <strong>Fecha de entrega:</strong>
                <?= htmlspecialchars(
                    (string) $order->delivered_at,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>
        <?php endif; ?>

        <!-- IMAGEN DE GUÍA -->
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