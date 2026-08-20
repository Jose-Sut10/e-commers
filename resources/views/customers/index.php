<h1>Clientes</h1>

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

<?php if (empty($customers)): ?>
    <p> Todavía no hay clientes registrados.</p>
<?php else: ?>

    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Pedidos</th>
                <th>Total gastado</th>
                <th>Último pedido</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>

        <?php foreach ($customers as $customer): ?>
            <tr>
                <td>
                    <strong>
                        <?= htmlspecialchars(
                            (string)
                            $customer['name'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </strong>
                </td>

                <td>
                    <?= htmlspecialchars(
                        (string)
                        $customer['phone'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        (string) (
                            $customer['email']
                            ?: '-'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </td>

                <td>
                    <?= (int) $customer['total_orders'] ?>
                </td>

                <td>
                    Q <?= number_format(
                        (float)
                        $customer['total_spent'],
                        2
                    ) ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        (string) (
                            $customer['last_order_at']
                            ?: 'Sin pedidos'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </td>

                <td>
                    <a href="<?= htmlspecialchars(
                        url(
                            'clientes/ver?id='
                            . (int)
                            $customer['id']
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">
                        Ver
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>