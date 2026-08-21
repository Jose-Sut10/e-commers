<h1>Editar cupón</h1>

<?php if (session('warning')): ?>

    <div class="alert alert-warning">
        <?= htmlspecialchars(
            (string) session('warning'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </div>

<?php endif; ?>


<form
    method="POST"
    action="<?= htmlspecialchars(
        url('cupones/actualizar'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>"
>

    <?= csrf_field() ?>

    <input
        type="hidden"
        name="id"
        value="<?= (int) $coupon->id ?>"
    >
    <?= csrf_field() ?>

    <div>
        <label for="code"> Código </label>

        <input
            id="code"
            type="text"
            name="code"
            maxlength="50"
            placeholder="CENTRO10"
            value="<?= htmlspecialchars(
                (string) old('code'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            required
        >
    </div>

    <div>

        <label for="type">Tipo de descuento</label>

        <select
            id="type"
            name="type"
            required
        >
            <option value="percentage">Porcentaje</option>
            <option value="fixed">Monto fijo</option>

        </select>
    </div>

    <div>
        <label for="value">Valor</label>

        <input
            id="value"
            type="number"
            name="value"
            min="0.01"
            step="0.01"
            value="<?= htmlspecialchars(
                (string) old(
                    'value',
                    $coupon->value
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            required
        >
    </div>

    <div>
        <label for="min_order"> Compra mínima</label>

        <input
            id="min_order"
            type="number"
            name="min_order"
            min="0"
            step="0.01"
            value="<?= htmlspecialchars(
                (string) old(
                    'min_order',
                    $coupon->min_order
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >
    </div>

    <div>

        <label for="usage_limit">Límite de usos</label>

        <input
            id="usage_limit"
            type="number"
            name="usage_limit"
            min="1"
            step="1"
            value="<?= htmlspecialchars(
                (string) old(
                    'usage_limit',
                    $coupon->usage_limit ?? ''
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <small> Déjalo vacío para usos ilimitados.</small>
    </div>

    <div>

        <label for="starts_at">Disponible desde</label>

        <input
            id="starts_at"
            type="datetime-local"
            name="starts_at"
        >
    </div>

    <div>
        <label for="ends_at">Válido hasta</label>
        <input
            id="ends_at"
            type="datetime-local"
            name="ends_at"
        >
    </div>

    <div>
        <label>
            <input
                type="checkbox"
                name="active"
                value= "<?= (bool) $coupon->active
                    ? 'checked'
                    : ''
                ?>"
                checked
            >
            Cupón activo
        </label>
    </div>

    <button type="submit">Guardar cupón</button>
</form>