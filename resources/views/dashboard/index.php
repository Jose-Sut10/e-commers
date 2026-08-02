<h1>Dashboard</h1>

<br>

<div class="cards">
    <?php
    component('card',[
        'title'=>'Productos',
        'value'=>0,
        'icon'=>'fa-box'
    ]);

    component('card',[
        'title'=>'Clientes',
        'value'=>0,
        'icon'=>'fa-users'
    ]);

    component('card',[
        'title'=>'Pedidos',
        'value'=>0,
        'icon'=>'fa-cart-shopping'
    ]);

    component('card',[
        'title'=>'Ventas',
        'value'=>'Q 0.00',
        'icon'=>'fa-coins'
    ]);
    ?>
</div>

<br><br>

<?php
    component('button',[
        'text'=>'Nuevo Producto',
        'color'=>'primary',
        'icon'=>'fa-plus'
    ]);
?>

&nbsp;

<?php
    component('button',[
        'text'=>'Exportar',
        'color'=>'success',
        'icon'=>'fa-file-excel'
    ]);
?>

&nbsp;

<?php
    component('button',[
        'text'=>'Eliminar',
        'color'=>'danger',
        'icon'=>'fa-trash'
    ]);
?>