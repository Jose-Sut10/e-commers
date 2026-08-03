<h1>Categorías</h1>
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
        url('categorias/crear'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        Registrar categoría
    </a>
</div>

<?php if (empty($categories)): ?>
    <p>No hay categorías registradas.</p>
<?php else: ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Slug</th>
                <th>Estado</th>
                <th>Fecha de registro</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td>
                        <?= (int) $category->id ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            (string) $category->name,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            (string) $category->slug,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        <?= (bool) $category->active
                            ? 'Activa'
                            : 'Inactiva'
                        ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            (string) $category->created_at,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        <a href="<?= htmlspecialchars(
                            url(
                                'categorias/editar?id='
                                . (int) $category->id
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>">
                            Editar
                        </a>

                        <form
                            method="POST"
                            action="<?= htmlspecialchars(
                                url('categorias/eliminar'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            onsubmit="return confirm(
                                '¿Estás seguro de eliminar esta categoría?'
                            );"
                            style="display: inline;"
                        >
                            <?= csrf_field() ?>

                            <input
                                type="hidden"
                                name="id"
                                value="<?= (int) $category->id ?>">

                            <button type="submit">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>