<h1>Métodos de envío</h1>

<?php if (session('success')): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars(
            (string) session('success'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </div>
<?php endif; ?>

<?php if (session('warning')): ?>
    <div class="alert alert-warning">
        <?= htmlspecialchars(
            (string) session('warning'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </div>
<?php endif; ?>

<section class="dashboard-section">
    <h2>Nuevo método</h2>

    <form
        method="POST"
        action="<?= htmlspecialchars(
            url('envios'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >
        <?= csrf_field() ?>

        <div>
            <label for="name">Nombre</label>
            <input
                id="name"
                type="text"
                name="name"
                maxlength="100"
                placeholder="Envío a domicilio"
                required
            >
        </div>

        <div>
            <label for="description"> Descripción</label>
            <textarea
                id="description"
                name="description"
                rows="3"
                placeholder="Entrega dentro de la ciudad..."
            ></textarea>
        </div>

        <div>
            <label for="price">Costo</label>

            <input
                id="price"
                type="number"
                name="price"
                min="0"
                step="0.01"
                value="0"
                required
            >
        </div>

        <div>
            <label for="sort_order">Orden</label>

            <input
                id="sort_order"
                type="number"
                name="sort_order"
                min="0"
                step="1"
                value="0"
            >
        </div>

        <div>
            <label>
                <input
                    type="checkbox"
                    name="active"
                    value="1"
                    checked
                >
                Método activo
            </label>
        </div>

        <button type="submit">Guardar método</button>
    </form>
</section>

<section class="dashboard-section">
    <h2>Métodos registrados</h2>

    <?php if (empty($methods)): ?>
        <p> Todavía no hay métodos de envío.</p>
    <?php else: ?>

        <?php foreach ($methods as $method): ?>
            <form
                method="POST"
                action="<?= htmlspecialchars(
                    url('envios/actualizar'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                style="
                    padding:20px 0;
                    border-bottom:1px solid #eee;
                "
            >
                <?= csrf_field() ?>

                <input
                    type="hidden"
                    name="id"
                    value="<?= (int) $method->id ?>"
                >

                <div>
                    <label> Nombre</label>
                    <input
                        type="text"
                        name="name"
                        maxlength="100"
                        value="<?= htmlspecialchars(
                            (string) $method->name,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        required
                    >
                </div>

                <div>
                    <label>Descripción</label>

                    <textarea
                        name="description"
                        rows="2"
                    ><?= htmlspecialchars(
                        (string) (
                            $method->description
                            ?? ''
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?></textarea>

                </div>

                <div>
                    <label>Costo</label>

                    <input
                        type="number"
                        name="price"
                        min="0"
                        step="0.01"
                        value="<?= htmlspecialchars(
                            (string) $method->price,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                </div>

                <div>
                    <label>Orden</label>
                    <input
                        type="number"
                        name="sort_order"
                        min="0"
                        step="1"
                        value="<?= (int) $method->sort_order ?>"
                    >
                </div>

                <div>
                    <label>
                        <input
                            type="checkbox"
                            name="active"
                            value="1"
                            <?= (bool) $method->active
                                ? 'checked'
                                : ''
                            ?>
                        >
                        Activo
                    </label>
                </div>

                <button type="submit"> Guardar cambios</button>
            </form>
        <?php endforeach; ?>
    <?php endif; ?>
</section>