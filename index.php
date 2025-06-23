<?php

require __DIR__ . '/vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar el archivo .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = new FrameworkX\App();

// Cargar rutas API y pasar el contenedor
$apiRoutes = require __DIR__ . '/src/Settings/api.php';
$apiRoutes($app);

require __DIR__ . '/src/Settings/view.php';

$app->run();