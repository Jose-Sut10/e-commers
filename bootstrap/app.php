<?php

use Core\Config;

Config::set(
    'app',
    require __DIR__ . '/../config/app.php'
);

Config::set(
    'database',
    require __DIR__ . '/../config/database.php'
);