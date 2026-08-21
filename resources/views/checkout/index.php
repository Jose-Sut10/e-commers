<h1>Finalizar compra</h1>

<?php if (error('general')): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars(
            (string) error('general'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </div>
<?php endif; ?>

<h2>Resumen del pedido</h2>

<table>
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Subtotal</th>
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
                    <?= htmlspecialchars(
                        (string) $product->name,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </td>

                <td>
                    <?= (int) $item['quantity'] ?>
                </td>

                <td>
                    Q <?= number_format(
                        (float) $product->price,
                        2
                    ) ?>
                </td>

                <td>
                    Q <?= number_format(
                        (float) $item['subtotal'],
                        2
                    ) ?>
                </td>
            </tr>

        <?php endforeach; ?>

    </tbody>
</table>

<h2>
    Total:
    Q <?= number_format(
        (float) $subtotal,
        2
    ) ?>
</h2>

<section class="dashboard-section">
    <h2>Cupón de descuento</h2>

    <?php if (
        !empty(
            $couponResult['coupon']
        )
    ): ?>
        <p>
            Cupón aplicado:

            <strong>
                <?= htmlspecialchars(
                    (string)
                    $couponResult['code'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </strong>
        </p>

        <p>
            Subtotal:

            Q <?= number_format(
                (float)
                $couponResult['subtotal'],
                2
            ) ?>
        </p>

        <p>
            Descuento:

            <strong>
                - Q <?= number_format(
                    (float)
                    $couponResult['discount'],
                    2
                ) ?>
            </strong>
        </p>

        <h2>
            Total:
            Q <?= number_format(
                (float)
                $couponResult['total'],
                2
            ) ?>
        </h2>

        <form
            method="POST"
            action="<?= htmlspecialchars(
                url(
                    'checkout/cupon/quitar'
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >
            <?= csrf_field() ?>

            <button type="submit">Quitar cupón</button>
        </form>

    <?php else: ?>

        <form
            method="POST"
            action="<?= htmlspecialchars(
                url('checkout/cupon'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >
            <?= csrf_field() ?>

            <div>
                <label for="coupon_code">Código</label>

                <input
                    id="coupon_code"
                    type="text"
                    name="coupon_code"
                    maxlength="50"
                    placeholder="CENTRO10"
                >
            </div>

            <button type="submit">Aplicar cupón </button>
        </form>
    <?php endif; ?>
</section>
<hr>

<h2>Datos del cliente</h2>

<form
    method="POST"
    action="<?= htmlspecialchars(
        url('checkout'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>"
>
    <?= csrf_field() ?>


    <div>
        <label for="name">
            Nombre completo
        </label>

        <input
            id="name"
            type="text"
            name="name"
            value="<?= htmlspecialchars(
                (string) old('name'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <?php if (error('name')): ?>
            <small class="form-error">
                <?= htmlspecialchars(
                    (string) error('name'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </small>
        <?php endif; ?>
    </div>

    <div>
        <label for="phone">Teléfono</label>

        <input
            id="phone"
            type="tel"
            name="phone"
            inputmode="numeric"
            maxlength="8"
            pattern="[0-9]{8}"
            placeholder="55551234"
            value="<?= htmlspecialchars(
                (string) old('phone'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <?php if (error('phone')): ?>
            <small class="form-error">
                <?= htmlspecialchars(
                    (string) error('phone'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </small>
        <?php endif; ?>
    </div>

    <div>
        <label for="email">
            Correo electrónico
        </label>

        <input
            id="email"
            type="email"
            name="email"
            value="<?= htmlspecialchars(
                (string) old('email'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <?php if (error('email')): ?>
            <small class="form-error">
                <?= htmlspecialchars(
                    (string) error('email'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </small>
        <?php endif; ?>
    </div>

    <div>
        <label for="address">Dirección de entrega</label>

        <textarea
            id="address"
            name="address"
            rows="4"
        ><?= htmlspecialchars(
            (string) old('address'),
            ENT_QUOTES,
            'UTF-8'
        ) ?></textarea>

        <?php if (error('address')): ?>
            <small class="form-error">
                <?= htmlspecialchars(
                    (string) error('address'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </small>
        <?php endif; ?>
    </div>

    <div>
        <label for="notes">
            Notas del pedido
        </label>

        <textarea
            id="notes"
            name="notes"
            rows="3"
        ><?= htmlspecialchars(
            (string) old('notes'),
            ENT_QUOTES,
            'UTF-8'
        ) ?></textarea>
    </div>

    <button type="submit">Confirmar pedido</button>

    <a href="<?= htmlspecialchars(
        url('carrito'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        Volver al carrito
    </a>
</form>