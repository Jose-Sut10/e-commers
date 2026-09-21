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
    enctype="multipart/form-data"
    action="<?= htmlspecialchars(
        url('checkout'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>"
>
    <?= csrf_field() ?>


    <div>
        <label for="name">Nombre completo</label>

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
        <label for="email">Correo electrónico</label>

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
        <label for="notes">Notas del pedido</label>

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

<div>
    <label for="shipping_method_id">Método de envío</label>

    <?php if (empty($shippingMethods)): ?>
        <div class="alert alert-warning">
            En este momento no hay métodos
            de envío disponibles.
        </div>
    <?php else: ?>

        <select
            id="shipping_method_id"
            name="shipping_method_id"
            required
        >
            <option value="">Seleccionar método de envío</option>

            <?php foreach (
                $shippingMethods
                as $method
            ): ?>

                <option
                    value="<?= (int) $method->id ?>"

                    data-price="<?= htmlspecialchars(
                        number_format(
                            (float) $method->price,
                            2,
                            '.',
                            ''
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"

                    <?= (string)
                        old('shipping_method_id')
                        ===
                        (string) $method->id
                            ? 'selected'
                            : ''
                    ?>
                >

                    <?= htmlspecialchars(
                        (string) $method->name,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                    —

                    <?php if (
                        (float) $method->price > 0
                    ): ?>

                        Q <?= number_format(
                            (float) $method->price,
                            2
                        ) ?>

                    <?php else: ?>
                        Gratis
                    <?php endif; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- MÉTODO DE PAGO -->
        <div>
            <label for="payment_method_id">Método de pago</label>

            <?php if (empty($paymentMethods)): ?>
                <div class="alert alert-warning">
                    En este momento no hay métodos
                    de pago disponibles.
                </div>

            <?php else: ?>

                <select
                    id="payment_method_id"
                    name="payment_method_id"
                    required
                >
                    <option value="">Seleccionar método de pago</option>

                    <?php foreach (
                        $paymentMethods
                        as $method
                    ): ?>
                        <option
                            value="<?= (int) $method->id ?>"
                            data-type="<?= htmlspecialchars(
                                (string) $method->type,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            data-instructions="<?= htmlspecialchars(
                                (string) (
                                    $method->instructions
                                    ?? ''
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            <?= (string)
                                old('payment_method_id')
                                ===
                                (string) $method->id
                                    ? 'selected'
                                    : ''
                            ?>
                        >
                            <?= htmlspecialchars(
                                (string) $method->name,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!--DATOS BANCARIOS-->

                <div
                    id="bank-transfer-details"
                    class="bank-transfer-details"
                >

                    <h3>Datos para transferencia</h3>

                    <?php if ($storeSettings->bank_name): ?>

                        <p>
                            <strong>Banco:</strong>
                            <?= htmlspecialchars(
                                (string)
                                $storeSettings->bank_name,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($storeSettings->bank_account_name): ?>

                        <p>
                            <strong>Nombre:</strong>
                            <?= htmlspecialchars(
                                (string)
                                $storeSettings->bank_account_name,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>

                    <?php endif; ?>

                    <?php if ($storeSettings->bank_account_number): ?>

                        <p>
                            <strong>Número de cuenta:</strong>
                            <?= htmlspecialchars(
                                (string)
                                $storeSettings->bank_account_number,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    <?php endif; ?>


                    <?php if ($storeSettings->bank_account_type): ?>

                        <p>
                            <strong>Tipo de cuenta:</strong>

                            <?= htmlspecialchars(
                                (string)
                                $storeSettings->bank_account_type,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    <?php endif; ?>
                </div>
                
                <!-- COMPROBANTE DE TRANSFERENCIA -->

                <div
                    id="payment-proof-section"
                    style="display:none;"
                >
                    <label for="payment_proof">Comprobante de transferencia</label>

                    <input
                        id="payment_proof"
                        type="file"
                        name="payment_proof"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    >

                    <small>
                        Adjunta una imagen JPG, PNG o WEBP.
                        Tamaño máximo: 2 MB.
                    </small>

                    <?php if (
                        error('payment_proof')
                    ): ?>
                        <small class="form-error">

                            <?= htmlspecialchars(
                                (string) error(
                                    'payment_proof'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <?php if (
                    error('payment_method_id')
                ): ?>
                    <small class="form-error">
                        <?= htmlspecialchars(
                            (string) error(
                                'payment_method_id'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </small>
                <?php endif; ?>

                <div
                    id="payment-method-instructions"
                    style="
                        display:none;
                        margin-top:12px;
                        padding:14px;
                        background:#f8f8f8;
                        border:1px solid #e5e5e5;
                        border-radius:8px;
                    "
                ></div>
            <?php endif; ?>
        </div>

        <?php if (
            error('shipping_method_id')
        ): ?>
            <small class="form-error">
                <?= htmlspecialchars(
                    (string) error(
                        'shipping_method_id'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </small>
        <?php endif; ?>
    <?php endif; ?>

</div>
<div
    class="checkout-total-box"
    data-base-total="<?= htmlspecialchars(
        number_format(
            (float) $couponResult['total'],
            2,
            '.',
            ''
        ),
        ENT_QUOTES,
        'UTF-8'
    ) ?>"
>
    <p>
        Subtotal:
        <strong>
            Q <?= number_format(
                (float)
                $couponResult['subtotal'],
                2
            ) ?>
        </strong>
    </p>

    <?php if (
        (float)
        $couponResult['discount'] > 0
    ): ?>
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
    <?php endif; ?>

    <p>
        Envío:
        <strong id="checkout-shipping">
            Q 0.00
        </strong>
    </p>

    <h2>
        Total:
        <span id="checkout-total">
            Q <?= number_format(
                (float)
                $couponResult['total'],
                2
            ) ?>
        </span>
    </h2>

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

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const select =
            document.getElementById(
                'shipping_method_id'
            );

        const box =
            document.querySelector(
                '.checkout-total-box'
            );

        const shippingElement =
            document.getElementById(
                'checkout-shipping'
            );

        const totalElement =
            document.getElementById(
                'checkout-total'
            );


        if (
            !select
            || !box
            || !shippingElement
            || !totalElement
        ) {
            return;
        }

        const baseTotal =
            parseFloat(
                box.dataset.baseTotal
                || '0'
            );

        function updateTotal() {

            const option =
                select.options[
                    select.selectedIndex
                ];


            const shipping =
                option
                    ? parseFloat(
                        option.dataset.price
                        || '0'
                    )
                    : 0;


            const total = baseTotal + shipping;


            shippingElement.textContent = 'Q ' + shipping.toFixed(2);
            totalElement.textContent = 'Q '
                + total.toFixed(2);
        }

        select.addEventListener('change',updateTotal);
        updateTotal();
    }
);
</script>

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {
        const paymentSelect =
            document.getElementById(
                'payment_method_id'
            );

        const instructionsBox = document.getElementById('payment-method-instructions');
        const proofSection = document.getElementById('payment-proof-section');
        const proofInput = document.getElementById('payment_proof');

        if (!paymentSelect) {
            return;
        }

        function updatePaymentMethod() {

            const option =
                paymentSelect.options[
                    paymentSelect.selectedIndex
                ];

            if (!option) {
                return;
            }

            /*INSTRUCCIONES*/

            const instructions = option.dataset.instructions || '';

            if (instructionsBox) {
                if (
                    instructions.trim()
                    !== ''
                ) {
                    instructionsBox.textContent = instructions;
                    instructionsBox.style.display = 'block';

                } else {
                    instructionsBox.textContent = '';
                    instructionsBox.style.display = 'none';
                }
            }

            /*COMPROBANTE*/

            const paymentType = option.dataset.type || '';
            const requiresProof =
                paymentType
                === 'bank_transfer';

            if (proofSection && proofInput) {

                if (requiresProof) {
                    proofSection.style.display = 'block';
                    proofInput.required = true;
                } else {
                    proofSection.style.display = 'none';
                    proofInput.required = false;

                    /*
                     * Limpiamos el archivo
                     * si cambia a efectivo.
                     */

                    proofInput.value = '';
                }
            }
        }

        paymentSelect.addEventListener('change',updatePaymentMethod);
        updatePaymentMethod();
    }
);
</script>