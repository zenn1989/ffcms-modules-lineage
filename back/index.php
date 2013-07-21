<?php
error_reporting(E_ALL ^ E_NOTICE);
define('root', $_SERVER['DOCUMENT_ROOT']);

require_once(root . "/config/main.php");
require_once(root . "/config/query.php");
require_once(root . "/engine/logging.class.php");
require_once(root . "/engine/system.class.php");
require_once(root . "/engine/database.class.php");
require_once(root . "/engine/core.class.php");

$system = new system();
$database = new database();
$core = new core();

echo $core->compile();

?>