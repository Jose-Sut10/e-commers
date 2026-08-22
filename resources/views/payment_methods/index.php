<h1>Métodos de pago</h1>
<?php
$success =session('success');
$warning =session('warning');
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

<!-- =====================================================
     CREAR MÉTODO
===================================================== -->

<section class="dashboard-section">
    <h2>Nuevo método de pago</h2>

    <form
        method="POST"
        action="<?= htmlspecialchars(
            url('metodos-pago'),
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
                placeholder="Transferencia bancaria"
                required
            >
        </div>

        <div>
            <label for="code"> Código interno</label>

            <input
                id="code"
                type="text"
                name="code"
                maxlength="50"
                placeholder="bank_transfer"
                required
            >
            <small>
                Usa letras minúsculas y sin espacios.
                Ejemplo: bank_transfer
            </small>
        </div>

        <div>
            <label for="type">Tipo</label>
            <select
                id="type"
                name="type"
                required
            >

                <option value="cash"> Efectivo / Contra entrega</option>
                <option value="bank_transfer">Transferencia bancaria</option>
                <option value="other">Otro</option>

            </select>
        </div>

        <div>
            <label for="instructions">Instrucciones para el cliente</label>

            <textarea
                id="instructions"
                name="instructions"
                rows="5"
                placeholder="Realiza la transferencia y conserva tu comprobante."
            ></textarea>
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

        <button type="submit"> Guardar método</button>

    </form>
</section>


<!-- =====================================================
     MÉTODOS REGISTRADOS
===================================================== -->

<section class="dashboard-section">
    <h2> Métodos registrados</h2>

    <?php if (empty($methods)): ?>
        <p> Todavía no hay métodos de pago registrados.</p>
    <?php else: ?>

        <?php foreach ($methods as $method): ?>

            <form
                method="POST"
                action="<?= htmlspecialchars(
                    url(
                        'metodos-pago/actualizar'
                    ),
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

                <h3>
                    <?= htmlspecialchars(
                        (string) $method->name,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </h3>

                <div>
                    <label>Nombre</label>

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
                    <label>Código</label>

                    <input
                        type="text"
                        name="code"
                        maxlength="50"
                        value="<?= htmlspecialchars(
                            (string) $method->code,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        required
                    >
                </div>

                <div>
                    <label>Tipo</label>
                    <select
                        name="type"
                        required
                    >

                        <option
                            value="cash"
                            <?= $method->type === 'cash'
                                ? 'selected'
                                : ''
                            ?>
                        >
                            Efectivo / Contra entrega
                        </option>

                        <option
                            value="bank_transfer"
                            <?= $method->type === 'bank_transfer'
                                ? 'selected'
                                : ''
                            ?>
                        >
                            Transferencia bancaria
                        </option>

                        <option
                            value="other"
                            <?= $method->type === 'other'
                                ? 'selected'
                                : ''
                            ?>
                        >
                            Otro
                        </option>
                    </select>
                </div>

                <div>
                    <label>Instrucciones</label>

                    <textarea
                        name="instructions"
                        rows="5"
                    ><?= htmlspecialchars(
                        (string) (
                            $method->instructions
                            ?? ''
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?></textarea>

                </div>

                <div>
                    <label>Orden</label>

                    <input
                        type="number"
                        name="sort_order"
                        min="0"
                        step="1"

                        value="<?= (int)
                            $method->sort_order
                        ?>"
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

                <p>
                    Tipo:
                    <strong>
                        <?= htmlspecialchars(
                            $method->typeLabel(),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </strong>
                </p>
                <button type="submit">Guardar cambios</button>
            </form>
        <?php endforeach; ?>
    <?php endif; ?>
</section>