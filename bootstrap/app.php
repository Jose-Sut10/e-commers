<?php
use Core\Config;

Config::set(
    'app',
    require BASE_PATH . '/config/app.php'
);

Config::set(
    'database',
    require BASE_PATH . '/config/database.php'
);

date_default_timezone_set(
    Config::get(
        'app.timezone',
        'America/Guatemala'
    )
);