<h1>Empresa</h1>

<?php $success = session('success'); ?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars(
            (string) $success,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </div>
<?php endif; ?>

<a href="<?= url('empresa/crear') ?>">Registrar empresa</a>