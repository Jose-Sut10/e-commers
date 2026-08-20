<h1>Pedidos</h1>
<?php

$success = session('success');
$warning = session('warning');
$orders = $paginator->items();
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

<form
    method="GET"
    action="<?= htmlspecialchars(
        url('pedidos'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>"
    class="filter-form"
>

    <div>
        <label for="q">Buscar</label>

        <input
            id="q"
            type="search"
            name="q"
            placeholder="Pedido, cliente, teléfono..."

            value="<?= htmlspecialchars(
                (string) $filters['q'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >
    </div>

    <div>
        <label for="status">Estado</label>

        <select
            id="status"
            name="status"
        >

            <option value="">Todos</option>

            <option
                value="pending"
                <?= $filters['status'] === 'pending'
                    ? 'selected'
                    : ''
                ?>
            >
                Pendientes
            </option>

            <option
                value="confirmed"
                <?= $filters['status'] === 'confirmed'
                    ? 'selected'
                    : ''
                ?>
            >
                Confirmados
            </option>

            <option
                value="shipped"
                <?= $filters['status'] === 'shipped'
                    ? 'selected'
                    : ''
                ?>
            >
                Enviados
            </option>

            <option
                value="delivered"
                <?= $filters['status'] === 'delivered'
                    ? 'selected'
                    : ''
                ?>
            >
                Entregados
            </option>

            <option
                value="cancelled"
                <?= $filters['status'] === 'cancelled'
                    ? 'selected'
                    : ''
                ?>
            >
                Cancelados
            </option>
        </select>
    </div>

    <div>
        <label for="date_from">Desde</label>

        <input
            id="date_from"
            type="date"
            name="date_from"

            value="<?= htmlspecialchars(
                (string)
                $filters['date_from'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >
    </div>

    <div>

        <label for="date_to">Hasta</label>

        <input
            id="date_to"
            type="date"
            name="date_to"

            value="<?= htmlspecialchars(
                (string)
                $filters['date_to'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >
    </div>

    <div class="filter-buttons">

        <button type="submit">Filtrar</button>

        <a href="<?= htmlspecialchars(
            url('pedidos'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>">
            Limpiar
        </a>
    </div>
</form>

<p class="pagination-summary">
    Mostrando
    <?= $paginator->from() ?>
    a
    <?= $paginator->to() ?>
    de
    <?= $paginator->total() ?>
    pedidos.
</p>

<?php if (empty($orders)): ?>
    <p>No se encontraron pedidos.</p>
<?php else: ?>

<table>
    <thead>
        <tr>
            <th>Pedido</th>
            <th>Cliente</th>
            <th>Teléfono</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th></th>
        </tr>
    </thead>

    <tbody>

    <?php foreach (
        $orders as $order
    ): ?>
        <tr>
            <td>
                <strong>
                    <?= htmlspecialchars(
                        (string)
                        $order->number,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </strong>
            </td>

            <td>
                <?= htmlspecialchars(
                    (string)
                    $order->customer_name,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </td>

            <td>
                <?= htmlspecialchars(
                    (string)
                    $order->customer_phone,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </td>

            <td>
                Q <?= number_format(
                    (float)
                    $order->total,
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
                    (string)
                    $order->created_at,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </td>

            <td>

                <a href="<?= htmlspecialchars(
                    url(
                        'pedidos/ver?id='
                        . (int)
                        $order->id
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

<?php
$paginationPath ='pedidos';
$paginationQuery =$filters;

require BASE_PATH
    . '/resources/views/partials/pagination.php';
?>