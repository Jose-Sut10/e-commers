<h1>Panel principal</h1>
<?php if ($user): ?>

    <p>
        Bienvenido,
        <strong>
            <?= htmlspecialchars(
                (string) $user->name,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </strong>
    </p>

<?php endif; ?>

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

<!-- =====================================================
     ESTADÍSTICAS
===================================================== -->
<section class="dashboard-stats">
    <h2>Resumen</h2>
    <div class="dashboard-cards">
        <!-- Ventas -->
        <article class="dashboard-card">
            <h3> Ventas procesadas</h3>

            <strong>
                Q <?= number_format(
                    (float)
                    $statistics['orders']['sales'],
                    2
                ) ?>
            </strong>
        </article>
        <!-- Ventas de hoy -->
        <article class="dashboard-card">
            <h3>Ventas de hoy</h3>
            <strong>
                Q <?= number_format(
                    (float)
                    $statistics['orders']['today_sales'],
                    2
                ) ?>
            </strong>
        </article>
        <!-- Pedidos pendientes -->
        <article class="dashboard-card">
            <h3>Pedidos pendientes</h3>

            <strong>
                <?= (int)
                    $statistics['orders']['pending']
                ?>
            </strong>

            <p>
                <a href="<?= htmlspecialchars(
                    url('pedidos'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>">
                    Ver pedidos
                </a>
            </p>
        </article>
        <!-- Total pedidos -->

        <article class="dashboard-card">
            <h3>Total de pedidos</h3>

            <strong>
                <?= (int)
                    $statistics['orders']['total']
                ?>
            </strong>

        </article>
        <!-- Productos -->
        <article class="dashboard-card">
            <h3>Productos</h3>

            <strong>
                <?= (int)
                    $statistics['products']['total']
                ?>
            </strong>

            <p>
                Activos:
                <?= (int)
                    $statistics['products']['active']
                ?>
            </p>

        </article>
        <!-- Stock bajo -->

        <article class="dashboard-card">

            <h3>Stock bajo</h3>

            <strong>
                <?= (int)
                    $statistics['products']['low_stock']
                ?>
            </strong>

            <p>
                Productos con 5 unidades
                o menos.
            </p>

        </article>
    </div>
</section>

<!-- =====================================================
     PEDIDOS RECIENTES
===================================================== -->

<section class="dashboard-section">
    <h2>Pedidos recientes</h2>
    <?php if (empty($recentOrders)): ?>
        <p>Todavía no hay pedidos registrados.</p>

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
                    $recentOrders as $order
                ): ?>

                    <?php
                    $statusLabel = match (
                        $order['status']
                    ) {
                        'pending' =>
                            'Pendiente',

                        'confirmed' =>
                            'Confirmado',

                        'shipped' =>
                            'Enviado',

                        'delivered' =>
                            'Entregado',

                        'cancelled' =>
                            'Cancelado',

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
                                $order['customer_name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                (string)
                                $order['customer_phone'],
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
                            <?= htmlspecialchars(
                                (string)
                                $order['created_at'],
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
                                Ver
                            </a>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p>
        <a href="<?= htmlspecialchars(
            url('pedidos'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>">
            Ver todos los pedidos
        </a>
    </p>
</section>
<!-- =====================================================
     PRODUCTOS CON POCO STOCK
===================================================== -->
<section class="dashboard-section">

    <h2>Productos con poco stock</h2>

    <?php if (empty($lowStockProducts)): ?>
        <p>No hay productos con existencias bajas.</p>
    <?php else: ?>

        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>SKU</th>
                    <th>Categoría</th>
                    <th>Existencias</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                <?php foreach (
                    $lowStockProducts as $product
                ): ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars(
                                (string)
                                $product['name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                (string)
                                $product['sku'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                (string)
                                $product['category_name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>
                            <strong>
                                <?= (int)
                                    $product['stock']
                                ?>
                            </strong>
                        </td>

                        <td>

                            <a href="<?= htmlspecialchars(
                                url(
                                    'inventario?id='
                                    . (int)
                                    $product['id']
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>">
                                Inventario
                            </a>
                        </td>
                    </tr>

                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<!-- =====================================================
     ACCESOS RÁPIDOS
===================================================== -->

<section class="dashboard-section">
    <h2>Accesos rápidos</h2>

    <div class="quick-actions">

        <a href="<?= htmlspecialchars(
            url('productos/crear'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>">
            Nuevo producto
        </a>

        <a href="<?= htmlspecialchars(
            url('categorias'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>">
            Categorías
        </a>

        <a href="<?= htmlspecialchars(
            url('productos'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>">
            Productos
        </a>

        <a href="<?= htmlspecialchars(
            url('pedidos'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>">
            Pedidos
        </a>

        <a href="<?= htmlspecialchars(
            url('usuarios'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>">
            Usuarios
        </a>

        <a
            href="<?= htmlspecialchars(
                url('tienda'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            target="_blank"
        >
            Ver tienda
        </a>
    </div>
</section>

<!-- =====================================================
     CERRAR SESIÓN
===================================================== -->
<hr>
<form
    method="POST"
    action="<?= htmlspecialchars(
        url('logout'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>"
>
    <?= csrf_field() ?>
    <button type="submit">Cerrar sesión</button>
</form>