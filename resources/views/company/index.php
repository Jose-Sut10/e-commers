<h1>Información de la empresa</h1>
<?php $success = session('success'); ?>
<?php $warning = session('warning'); ?>

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

<?php if ($company): ?>
    <section class="company-information">
        <div>
            <strong>Nombre de la empresa:</strong>
            <span>
                <?= htmlspecialchars(
                    (string) $company->name,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </span>
        </div>

        <div>
            <strong>Correo electrónico:</strong>
            <span>
                <?= htmlspecialchars(
                    (string) (
                        $company->email
                        ?: 'No registrado'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </span>
        </div>

        <div>
            <strong>Impuesto:</strong>
            <span>
                <?= number_format(
                    (float) $company->tax,
                    2
                ) ?>%
            </span>
        </div>

        <div>
            <strong>Fecha de registro:</strong>
            <span>
                <?= htmlspecialchars(
                    (string) $company->created_at,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </span>
        </div>
    </section>

    <div class="company-actions">
        <a href="<?= htmlspecialchars(
            url('empresa/editar'),
            ENT_QUOTES,
            'UTF-8'
            ) ?>">
                Editar información
        </a>
    </div>

<?php else: ?>

    <p> Todavía no hay una empresa registrada.</p>

    <a href="<?= htmlspecialchars(
        url('empresa/crear'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        Registrar empresa
    </a>
<?php endif; ?>