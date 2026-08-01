<div class="card">

    <?php if (!empty($icon)): ?>
        <div class="card-icon">
            <i class="fa-solid <?= $icon ?>"></i>
        </div>
    <?php endif; ?>

    <div class="card-body">

        <h4><?= $title ?? '' ?></h4>

        <h2><?= $value ?? 0 ?></h2>

        <?php if (!empty($subtitle)): ?>

            <small><?= $subtitle ?></small>

        <?php endif; ?>

    </div>

</div>