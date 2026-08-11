<section class="error-page">
    <div class="tape tape-left"></div>
    <div class="tape tape-right"></div>

    <div class="label-card">
        <div class="stamp">Extraviado</div>

        <div class="eyebrow-row">
            <span>Seguimiento · manifiesto</span>
            <div class="barcode" aria-hidden="true">
                <span style="height:14px"></span><span style="height:20px"></span><span style="height:10px"></span>
                <span style="height:18px"></span><span style="height:14px"></span><span style="height:20px"></span>
                <span style="height:10px"></span><span style="height:16px"></span><span style="height:20px"></span>
                <span style="height:12px"></span><span style="height:18px"></span>
            </div>
        </div>

        <h1>404</h1>
        <h2>Este producto se perdió en el almacén</h2>
        <p class="desc">
            El enlace que buscas está roto o el artículo ya no está disponible en el catálogo.
            Puedes buscarlo de nuevo o volver a la tienda para seguir explorando.
        </p>

        <div class="manifest">
            <div class="row"><span class="k">Estado</span><span class="v flag">No entregado</span></div>
            <div class="row"><span class="k">Motivo</span><span class="v">Página no encontrada</span></div>
            <div class="row"><span class="k">Fecha</span><span class="v"><?= date('d \d\e F \d\e Y') ?></span></div>
        </div>

        <form class="search-row" role="search" action="<?= htmlspecialchars(url('buscar'), ENT_QUOTES, 'UTF-8') ?>" method="get">
            <input type="search" name="q" placeholder="Buscar productos, marcas, categorías…" aria-label="Buscar en la tienda">
            <button type="submit">Buscar</button>
        </form>

        <a class="cta" href="<?= htmlspecialchars(url('tienda'), ENT_QUOTES, 'UTF-8') ?>">Ir a la tienda</a>

        <div class="quick-links">
            <span>O explora:</span>
            <a href="<?= htmlspecialchars(url('novedades'), ENT_QUOTES, 'UTF-8') ?>">Novedades</a>
            <a href="<?= htmlspecialchars(url('ofertas'), ENT_QUOTES, 'UTF-8') ?>">Ofertas</a>
            <a href="<?= htmlspecialchars(url('mas-vendidos'), ENT_QUOTES, 'UTF-8') ?>">Más vendidos</a>
        </div>
    </div>
</section>