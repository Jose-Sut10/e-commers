<h1>Carrito de compras</h1>
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

<?php if (empty($items)): ?>

    <p>Tu carrito está vacío.</p>

    <a href="<?= htmlspecialchars(
        url('tienda'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        Ver productos
    </a>

<?php else: ?>

    <table>
        <thead>
            <tr>
                <th>Imagen</th>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>

        <?php foreach ($items as $item): ?>

            <?php
            $product =
                $item['product'];
            ?>

            <tr>
                <td>
                    <?php if ($product->image_path): ?>

                        <img
                            src="<?= htmlspecialchars(
                                asset(
                                    (string) $product->image_path
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            alt="<?= htmlspecialchars(
                                (string) $product->name,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            width="80"
                            height="80"
                            style="object-fit: cover;"
                        >

                    <?php else: ?>
                        Sin imagen
                    <?php endif; ?>
                </td>

                <td>
                    <a href="<?= htmlspecialchars(
                        url(
                            'producto?slug='
                            . urlencode(
                                (string) $product->slug
                            )
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">
                        <?= htmlspecialchars(
                            (string) $product->name,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </a>
                </td>

                <td>
                    Q <?= number_format(
                        (float) $product->price,
                        2
                    ) ?>
                </td>

                <td>
                    <form
                        method="POST"
                        action="<?= htmlspecialchars(
                            url(
                                'carrito/actualizar'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >
                        <?= csrf_field() ?>

                        <input
                            type="hidden"
                            name="product_id"
                            value="<?= (int) $product->id ?>"
                        >

                        <input
                            type="number"
                            name="quantity"
                            min="0"
                            max="<?= (int) $product->stock ?>"
                            value="<?= (int) $item['quantity'] ?>"
                        >

                        <button type="submit">
                            Actualizar
                        </button>
                    </form>
                </td>

                <td>
                    Q <?= number_format(
                        (float) $item['subtotal'],
                        2
                    ) ?>
                </td>

                <td>
                    <form
                        method="POST"
                        action="<?= htmlspecialchars(
                            url(
                                'carrito/eliminar'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >
                        <?= csrf_field() ?>

                        <input
                            type="hidden"
                            name="product_id"
                            value="<?= (int) $product->id ?>"
                        >

                        <button type="submit">
                            Eliminar
                        </button>
                    </form>
                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>
    </table>

    <h2>
        Subtotal:
        Q <?= number_format(
            (float) $subtotal,
            2
        ) ?>
    </h2>

    <p>
        <a href="<?= htmlspecialchars(
            url('checkout'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>">
            Finalizar compra
        </a>
    </p>

    <form
        method="POST"
        action="<?= htmlspecialchars(
            url('carrito/vaciar'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
        onsubmit="return confirm(
            '¿Vaciar todo el carrito?'
        );"
    >
        <?= csrf_field() ?>

        <button type="submit">
            Vaciar carrito
        </button>
    </form>

    <p>
        <a href="<?= htmlspecialchars(
            url('tienda'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>">
            Seguir comprando
        </a>
    </p>
<?php endif; ?>