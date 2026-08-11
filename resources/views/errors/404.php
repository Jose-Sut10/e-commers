<section class="error-page">
    <h1>404</h1>

    <h2>Página no encontrada</h2>

    <p>
        El contenido que buscas no existe
        o ya no está disponible.
    </p>

    <a href="<?= htmlspecialchars(
        url('tienda'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        Ir a la tienda
    </a>
</section>