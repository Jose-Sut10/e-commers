<?php
$error =
    session(
        'tracking_error'
    );

$oldTracking =
    session(
        'tracking_old',
        []
    );

$formNumber =
    isset($number)
        ? $number
        : (
            $oldTracking['number']
            ?? ''
        );

$formPhone =
    isset($phone)
        ? $phone
        : (
            $oldTracking['phone']
            ?? ''
        );

/*ESTADO ACTUAL*/

$currentStep = 0;

if ($order) {

    $currentStep =
        match (
            (string) $order->status
        ) {
            'pending' => 1,
            'confirmed' => 2,
            'shipped' => 3,
            'delivered' => 4,
            default => 0,
        };
}

$statusLabels = [
    'pending' => 'Pedido recibido',
    'confirmed' => 'Pedido confirmado',
    'shipped' => 'Pedido enviado',
    'delivered' => 'Pedido entregado',
    'cancelled' => 'Pedido cancelado',
];
?>

<div class="tracking-page">

    <!-- =================================================
         ENCABEZADO
    ================================================== -->
    <div class="tracking-heading">
        <h1>Seguimiento de pedido</h1>

        <p>
            Ingresa tu número de pedido y el
            teléfono utilizado al realizar la compra.
        </p>
    </div>

    <!-- =================================================
         BUSCADOR
    ================================================== -->

    <section class="tracking-search-card">

        <?php if ($error): ?>
            <div class="alert alert-warning">

                <?= htmlspecialchars(
                    (string) $error,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>
        <?php endif; ?>

        <form
            method="POST"
            action="<?= htmlspecialchars(
                url('seguimiento'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >
            <?= csrf_field() ?>

            <div>
                <label for="number">Número de pedido</label>

                <input
                    id="number"
                    type="text"
                    name="number"
                    maxlength="40"

                    placeholder="ORD-20260824-ABC123"

                    value="<?= htmlspecialchars(
                        (string) $formNumber,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"

                    required
                >
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
                        (string) $formPhone,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required
                >
            </div>
            <button type="submit">Consultar pedido</button>
        </form>
    </section>

    <!-- =================================================
         SIN RESULTADO
    ================================================== -->
    <?php if ($searched && !$order): ?>

        <section class="tracking-result-card">
            <div class="alert alert-warning">
                No encontramos un pedido que
                coincida con los datos ingresados.
            </div>

            <p>
                Revisa que el número de pedido
                y el teléfono sean exactamente
                los utilizados al realizar la compra.
            </p>
        </section>
    <?php endif; ?>

    <!-- =================================================
         PEDIDO ENCONTRADO
    ================================================== -->
    <?php if ($order): ?>
        <section class="tracking-result-card">

            <div class="tracking-order-header">
                <div>
                    <span class="tracking-label"> Pedido</span>

                    <h2>
                        <?= htmlspecialchars(
                            (string) $order->number,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h2>
                </div>

                <div class="tracking-status-badge">
                    <?= htmlspecialchars(
                        $statusLabels[
                            $order->status
                        ] ?? $order->status,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>
            </div>

            <!-- =========================================
                 PEDIDO CANCELADO
            ========================================== -->

            <?php if (
                $order->status
                === 'cancelled'
            ): ?>
                <div class="tracking-cancelled">

                    <strong>Este pedido fue cancelado.</strong>
                    <p>
                        Si necesitas más información,
                        comunícate con la tienda.
                    </p>
                </div>
            <?php else: ?>

                <!-- =====================================
                     PROGRESO
                ====================================== -->
                <div class="tracking-progress">
                    <!-- PASO 1 -->
                    <div
                        class="tracking-step
                        <?= $currentStep >= 1
                            ? 'is-complete'
                            : ''
                        ?>"
                    >
                        <span class="tracking-step-number">1</span>
                        <div>
                            <strong>Pedido recibido</strong>
                            <small>Recibimos tu solicitud.</small>
                        </div>
                    </div>

                    <!-- PASO 2 -->

                    <div
                        class="tracking-step
                        <?= $currentStep >= 2
                            ? 'is-complete'
                            : ''
                        ?>"
                    >
                        <span class="tracking-step-number">2</span>
                        <div>
                            <strong>Confirmado</strong>
                            <small>Tu pedido fue confirmado.</small>
                        </div>
                    </div>

                    <!-- PASO 3 -->
                    <div
                        class="tracking-step
                        <?= $currentStep >= 3
                            ? 'is-complete'
                            : ''
                        ?>"
                    >
                        <span class="tracking-step-number">3</span>

                        <div>
                            <strong>Enviado</strong>
                            <small>Tu pedido salió para entrega.</small>
                        </div>
                    </div>

                    <!-- PASO 4 -->
                    <div
                        class="tracking-step
                        <?= $currentStep >= 4
                            ? 'is-complete'
                            : ''
                        ?>"
                    >
                        <span class="tracking-step-number">4</span>

                        <div>
                            <strong>Entregado</strong>
                            <small>Pedido entregado.</small>
                        </div>
                    </div>
                </div>

                <!-- =====================================
                     INFORMACIÓN
                ====================================== -->

                <div class="tracking-information">
                    <div>
                        <span>Estado</span>

                        <strong>

                            <?= htmlspecialchars(
                                $statusLabels[
                                    $order->status
                                ] ?? $order->status,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                    </div>

                    <?php if ($order->shipping_method_name): ?>
                        <div>
                            <span>Método de envío</span>

                            <strong>
                                <?= htmlspecialchars(
                                    (string)
                                    $order->shipping_method_name,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </strong>
                        </div>
                    <?php endif; ?>

                    <?php if ($order->shipping_carrier): ?>

                        <div>
                            <span>Transportista</span>

                            <strong>

                                <?= htmlspecialchars(
                                    (string)
                                    $order->shipping_carrier,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </strong>
                        </div>
                    <?php endif; ?>

                    <?php if ($order->tracking_number): ?>

                        <div>
                            <span>Número de guía</span>

                            <strong>
                                <?= htmlspecialchars(
                                    (string)
                                    $order->tracking_number,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </strong>
                        </div>
                    <?php endif; ?>

                    <?php if ($order->shipped_at): ?>
                        <div>
                            <span>Fecha de despacho</span>
                            <strong>

                                <?= htmlspecialchars(
                                    (string)
                                    $order->shipped_at,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </strong>
                        </div>
                    <?php endif; ?>

                    <?php if ($order->delivered_at): ?>

                        <div>
                            <span>Fecha de entrega</span>

                            <strong>
                                <?= htmlspecialchars(
                                    (string)
                                    $order->delivered_at,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </strong>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</div>