<a href="<?= htmlspecialchars(
    url('clientes'),
    ENT_QUOTES,
    'UTF-8'
) ?>">
    ← Volver a clientes
</a>

<h1>
    <?= htmlspecialchars(
        (string) $customer['name'],
        ENT_QUOTES,
        'UTF-8'
    ) ?>
</h1>

<section class="dashboard-section">
    <h2>Información del cliente</h2>

    <p>
        <strong>Teléfono:</strong>
        <?= htmlspecialchars(
            (string) $customer['phone'],
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </p>

    <p>
        <strong>Correo:</strong>
        <?= htmlspecialchars(
            (string) (
                $customer['email']
                ?: 'No registrado'
            ),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </p>

    <p>
        <strong>Dirección:</strong>
        <?php if ($customer['address']): ?>

            <?= nl2br(
                htmlspecialchars(
                    (string)
                    $customer['address'],
                    ENT_QUOTES,
                    'UTF-8'
                )
            ) ?>

        <?php else: ?>
            No registrada
        <?php endif; ?>
    </p>
</section>

<div class="dashboard-cards">

    <article class="dashboard-card">

        <h3> Pedidos </h3>

        <strong>
            <?= (int)
                $customer['total_orders']
            ?>
        </strong>

    </article>

    <article class="dashboard-card">

        <h3> Total gastado </h3>

        <strong>
            Q <?= number_format(
                (float)
                $customer['total_spent'],
                2
            ) ?>
        </strong>

    </article>

    <article class="dashboard-card">
        <h3> Último pedido </h3>

        <strong style="font-size:16px;">
            <?= htmlspecialchars(
                (string) (
                    $customer['last_order_at']
                    ?: 'Sin pedidos'
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </strong>
    </article>
</div>

<section class="dashboard-section">

    <h2> Historial de pedidos</h2>

    <?php if (empty($orders)): ?>
        <p>
            Este cliente todavía no tiene
            pedidos asociados.
        </p>
    <?php else: ?>

        <table>
            <thead>

                <tr>
                    <th>Pedido</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th></th>
                </tr>

            </thead>

            <tbody>

            <?php foreach (
                $orders as $order
            ): ?>

                <?php
                $statusLabel =
                    match (
                        $order['status']
                    ) {
                        'pending' => 'Pendiente',
                        'confirmed' =>'Confirmado',
                        'shipped' => 'Enviado',
                        'delivered' => 'Entregado',
                        'cancelled' => 'Cancelado',
                        default =>
                            'Desconocido',
                    };
                ?>

                <tr>
                    <td>
                        <?= htmlspecialchars(
                            (string)
                            $order['number'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            (string)
                            $order['created_at'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>

                        Q <?= number_format(
                            (float)
                            $order['total'],
                            2
                        ) ?>

                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $statusLabel,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        <a href="<?= htmlspecialchars(
                            url(
                                'pedidos/ver?id='
                                . (int)
                                $order['id']
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>">
                            Ver pedido
                        </a>
                    </td>

                </tr>

            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>