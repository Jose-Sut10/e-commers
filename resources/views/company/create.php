<h1>Registrar empresa</h1>
<form
    method="POST"
    action="<?= url('empresa') ?>">
    
    <div>
        <label for="name">Nombre de la empresa</label>

        <input
            id="name"
            type="text"
            name="name"
            value="<?= htmlspecialchars(
                (string) old('name')
            ) ?>">

        <?php if (error('name')): ?>
            <small class="form-error">
                <?= htmlspecialchars(error('name')) ?>
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
                (string) old('email')
            ) ?>">

        <?php if (error('email')): ?>
            <small class="form-error">
                <?= htmlspecialchars(error('email')) ?>
            </small>
        <?php endif; ?>
    </div>

    <div>
        <label for="tax">Impuesto</label>

        <input
            id="tax"
            type="number"
            step="0.01"
            name="tax"
            value="<?= htmlspecialchars(
                (string) old('tax', '12')
            ) ?>">

        <?php if (error('tax')): ?>
            <small class="form-error">
                <?= htmlspecialchars(error('tax')) ?>
            </small>
        <?php endif; ?>
    </div>

    <button type="submit">Guardar</button>
</form>