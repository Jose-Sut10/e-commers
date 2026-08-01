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