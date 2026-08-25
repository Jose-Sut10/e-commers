<section class="order-success">
    <h1>¡Pedido recibido!</h1>
    <p>Tu pedido fue registrado correctamente.</p>
    <p>Número de pedido:</p>

    <h2>
        <?= htmlspecialchars(
            (string) $number,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </h2>

    <p>
        Guarda tu número de pedido.
        Lo necesitarás junto con tu teléfono
        para consultar el estado de tu compra.

    </p>

    <a
        href="<?= htmlspecialchars(
            url('seguimiento'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >
        Consultar seguimiento
    </a>

    <a href="<?= htmlspecialchars(
        url('tienda'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        Volver a la tienda
    </a>
</section>