<button
    class="btn btn-<?= $color ?? 'primary' ?>"
    type="<?= $type ?? 'button' ?>">
    
    <?php if(!empty($icon)): ?>
        <i class="fa-solid <?= $icon ?>"></i>
    <?php endif; ?>

    <?= $text ?? 'Botón' ?>
</button>