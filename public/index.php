<?php
define('BASE_PATH', dirname(__DIR__));

require_once '../core/Router.php';

$router = new Router();
$router->handleRequest();