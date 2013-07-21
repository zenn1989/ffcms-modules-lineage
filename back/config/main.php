<?php
$main = array();

// секретный ключ для работы API
$main['secret_api_key'] = 'Aue4QjaEYAZjM';

// конфигурация подключения к базе данных сервера №1 - логин сервер
$main['server'][1]['login']['db_host'] = 'localhost'; // database host
$main['server'][1]['login']['db_user'] = 'mysql'; // database user
$main['server'][1]['login']['db_pass'] = 'mysql'; // database password
$main['server'][1]['login']['db_name'] = 'l2jdb'; // database name
$main['server'][1]['login']['ip'] = '127.0.0.1'; // login server IP
$main['server'][1]['login']['port'] = '2106'; // login server PORT


// конфигурация подключения к базе данных сервера №1 - гейм сервер
$main['server'][1]['game']['db_host'] = 'localhost'; // database host
$main['server'][1]['game']['db_user'] = 'mysql'; // database user
$main['server'][1]['game']['db_pass'] = 'mysql'; // database password
$main['server'][1]['game']['db_name'] = 'l2jdb'; // database name
$main['server'][1]['game']['ip'] = '127.0.0.1'; // game server IP
$main['server'][1]['game']['port'] = '7777'; // game server PORT
$main['server'][1]['game']['online_add'] = 0; // additional online (sum method)
$main['server'][1]['game']['online_mul'] = 1; // multiply online (mul method)
// online_total = (real_online+online_add) * online_mul;

?>