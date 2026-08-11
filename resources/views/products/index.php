<h1>Productos</h1>
<?php $success = session('success'); ?>
<?php $warning = session('warning'); ?>

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

<div class="page-actions">
    <a href="<?= htmlspecialchars(
        url('productos/crear'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        Registrar producto
    </a>
</div>

<?php if (empty($products)): ?>
    <p>No hay productos registrados.</p>
<?php else: ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>SKU</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Existencias</th>
                <th>Estado</th>
                <th>Registro</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td>
                        <?= (int) $product->id ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            (string) $product->name,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            (string) $product->sku,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            (string) $product->category_name,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        Q <?= number_format(
                            (float) $product->price,
                            2
                        ) ?>
                    </td>

                    <td>
                        <?= (int) $product->stock ?>
                    </td>

                    <td>
                        <?= (bool) $product->active
                            ? 'Activo'
                            : 'Inactivo'
                        ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            (string) $product->created_at,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>