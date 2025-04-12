<?php

require __DIR__ . '/vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar el archivo .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
// Cargar el contenedor de dependencias
$containerFactory = require __DIR__ . '/src/Settings/dependencies.php';
$container = $containerFactory();

use AppPHP\RedPay\Settings\XContainerAdaptador;

$adaptadorContenedor = new XContainerAdaptador($container);

$app = new FrameworkX\App($adaptadorContenedor);

// Cargar rutas API y pasar el contenedor
$apiRoutes = require __DIR__ . '/src/Settings/api.php';
$apiRoutes($app, $adaptadorContenedor);

require __DIR__ . '/src/Settings/view.php';

$app->run();