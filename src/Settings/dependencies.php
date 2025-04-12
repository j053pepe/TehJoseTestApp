<?php

use DI\Container;
use GuzzleHttp\Client; // Importamos la clase Client de Guzzle
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use AppPHP\RedPay\Repositories\UserRepository;
use AppPHP\RedPay\Repositories\WebhookRepository;
use AppPHP\RedPay\Repositories\PagoRepository;
use AppPHP\RedPay\Repositories\PaisRepository;
use AppPHP\RedPay\Repositories\CompanyRepository;

use AppPHP\RedPay\Controllers\AccountController;
use AppPHP\RedPay\Controllers\WebHookController;
use AppPHP\RedPay\Controllers\MessageController;
use AppPHP\RedPay\Controllers\PagoController;
use AppPHP\RedPay\Controllers\PaisController;

use AppPHP\RedPay\Middlewares\TokenMiddleware;
use AppPHP\RedPay\Services\PagoService;
use AppPHP\RedPay\Services\ApiService;

return function () {
    
    // Crear un contenedor con PHP-DI
    $container = new Container();

    // Logger
    $container->set(Logger::class, function () {
        $logger = new Logger('myAppLogger');
        $logPath = __DIR__ . '/../../app.log'; // Ajusta la ruta si es necesario
        $logger->pushHandler(new StreamHandler($logPath, Logger::DEBUG));
        return $logger;
    });

    // Repositorios
    $container->set(UserRepository::class, DI\autowire());
    $container->set(WebhookRepository::class, DI\autowire());
    $container->set(PagoRepository::class, DI\autowire());
    $container->set(PaisRepository::class, DI\autowire());
    $container->set(CompanyRepository::class, DI\autowire());

    // Services
    $container->set(PagoService::class, DI\autowire());
    $container->set(ApiEnvioService::class, function () {
        return new Client(); // Creamos una instancia de Guzzle Client
    });
    // Controladores
    $container->set(AccountController::class, DI\autowire());
    $container->set(WebHookController::class, DI\autowire());
    $container->set(MessageController::class, DI\autowire());
    $container->set(PagoController::class, DI\autowire());
    $container->set(PaisController::class, DI\autowire());

    // Middleware
    $container->set(TokenMiddleware::class, DI\autowire());

    return $container;
};
