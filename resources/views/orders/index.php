<h1>Pedidos</h1>
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

<?php if (empty($orders)): ?>
    <p>Todavía no hay pedidos registrados.</p>
<?php else: ?>

    <table>

        <thead>
            <tr>
                <th>ID</th>
                <th>Pedido</th>
                <th>Cliente</th>
                <th>Teléfono</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>

            <?php foreach ($orders as $order): ?>

                <tr>

                    <td>
                        <?= (int) $order->id ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            (string) $order->number,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            (string) $order->customer_name,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            (string) $order->customer_phone,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        Q <?= number_format(
                            (float) $order->total,
                            2
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $order->statusLabel(),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            (string) $order->created_at,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        <a href="<?= htmlspecialchars(
                            url(
                                'pedidos/ver?id='
                                . (int) $order->id
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