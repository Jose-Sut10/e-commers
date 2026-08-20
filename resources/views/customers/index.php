<h1>Clientes</h1>

<?php
$warning = session('warning');
$customers = $paginator->items();
?>

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
        url('clientes'),
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
            placeholder="Nombre, teléfono o correo"

            value="<?= htmlspecialchars(
                (string) $filters['q'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >
    </div>

    <div>

        <label for="active"> Estado</label>

        <select
            id="active"
            name="active"
        >

            <option value="">Todos</option>

            <option
                value="1"
                <?= $filters['active'] === '1'
                    ? 'selected'
                    : ''
                ?>
            >
                Activos
            </option>

            <option
                value="0"
                <?= $filters['active'] === '0'
                    ? 'selected'
                    : ''
                ?>
            >
                Inactivos
            </option>
        </select>
    </div>

    <div>

        <label for="orders">Pedidos</label>

        <select
            id="orders"
            name="orders"
        >

            <option value="">Todos</option>

            <option
                value="with"
                <?= $filters['orders'] === 'with'
                    ? 'selected'
                    : ''
                ?>
            >
                Con pedidos
            </option>

            <option
                value="without"
                <?= $filters['orders'] === 'without'
                    ? 'selected'
                    : ''
                ?>
            >
                Sin pedidos
            </option>
        </select>
    </div>

    <div class="filter-buttons">
        <button type="submit">Buscar</button>

        <a href="<?= htmlspecialchars(
            url('clientes'),
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
    clientes.
</p>

<?php if (empty($customers)): ?>
    <p>No se encontraron clientes.</p>
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
            <th></th>
        </tr>
    </thead>

    <tbody>

    <?php foreach (
        $customers as $customer
    ): ?>
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
                <?= (int)
                    $customer['total_orders']
                ?>
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

<?php

$paginationPath = 'clientes';
$paginationQuery = $filters;

require BASE_PATH
    . '/resources/views/partials/pagination.php';

?>