<?php

use FrameworkX\App;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
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
use AppPHP\RedPay\Controllers\LogViewerController;

use AppPHP\RedPay\Middlewares\TokenMiddleware;
use AppPHP\RedPay\Services\PagoService;
use AppPHP\RedPay\Services\ApiService;
use AppPHP\RedPay\Services\AppLoggerService;

return function (App $app) {
    // Crear un cliente Guzzle para las peticiones HTTP
    $guzzleClient = new Client();
    // Logger
    $logPath = __DIR__ . '/../../app.log';
    $logger = new Logger('myAppLogger');
    $logger->pushHandler(new StreamHandler($logPath, Logger::DEBUG));
    // Registrar Servicio de Logger
    $serviceLogger = new AppLoggerService($logger);
    // Crear una instancia del servicio de API
    $apiService = new ApiService($guzzleClient, $serviceLogger);
    // Crear una instancia del servicio de pago
    $pagoService = new PagoService(
        new PagoRepository(),
        new CompanyRepository(),
        $apiService,
        $serviceLogger
    );    
    // Middleware de token
    $tokenMiddleware = new TokenMiddleware($serviceLogger);
    // AccountController
    $userRepository = new UserRepository();
    $accountController = new AccountController($userRepository, $serviceLogger);
    // MessageController
    $messageController = new MessageController();
    // WebhookController
    $webhookRepository = new WebhookRepository();
    $webhookController = new WebHookController($webhookRepository, $pagoService, $serviceLogger);
    // PagoController
    $pagoController = new PagoController($pagoService, $serviceLogger);
    // PaisController
    $paisRepository = new PaisRepository();
    $paisController = new PaisController($paisRepository);
    // LoggerController
    $loggerController = new LogViewerController();
    // Definir las rutas de la API
    $app->get('/api', function () {
        return new Response(200, ['Content-Type' => 'text/plain'], "Hello everybody!\n");
    });

    $app->get('/api/message/{name}', [$messageController, 'home']);
    $app->post('/api/message', [$messageController, 'hello']);
    $app->get('/api/webhook', [$messageController, 'GetAllWebhookReponse']);

    $app->post('/api/account/login', [$accountController, 'login']);
    $app->post('/api/account/register', $tokenMiddleware, [$accountController, 'register']); // Modificado

    $app->get('/api/validate-token', function (ServerRequestInterface $request) use ($tokenMiddleware) {
        $headers = $request->getHeaders();
        $token = $headers['Authorization'][0] ?? null;
        if ($token === null || !str_starts_with($token, 'Bearer ')) {
            return new Response(500, ['Content-Type' => 'application/json'], json_encode([
                'valid' => false,
                'message' => 'Token no proporcionado o formato inválido',
                'status' => 'error',
                'headers' => $headers
            ]));
        }

        $result = $tokenMiddleware->validateToken($token);
        return new Response(200, ['Content-Type' => 'application/json'], json_encode($result));
    });

    $app->post('/api/webhook/redpay', [$webhookController, 'redpay']);
    $app->get('/api/webhook/redpay', [$webhookController, 'redpay']);
    $app->get('/api/webhook/get', $tokenMiddleware, [$webhookController, 'get']);
    $app->get('/api/webhook/getDetail/{id}', $tokenMiddleware, [$webhookController, 'getLogById']);
    $app->post('/api/pagar', $tokenMiddleware, [$pagoController, 'Create']);
    $app->get('/api/pagar/reference/{reference}', $tokenMiddleware, [$pagoController, 'GetByReference']);
    $app->get('/api/compra', $tokenMiddleware, [$pagoController, 'GetAll']);
    $app->get('/api/pais',$tokenMiddleware,[$paisController,'GetAll']);
    $app->get('/api/pais/{code}',$tokenMiddleware,[$paisController,'GetByCode']);
    $app->get('/api/user', $tokenMiddleware, [$accountController, 'GetAll']);
    $app->put('/api/user/status/{id}', $tokenMiddleware, [$accountController, 'UpdateStatus']);
    $app->put('/api/user/{id}', $tokenMiddleware, [$accountController, 'Update']);
    $app->get('/api/log', [$loggerController, 'get']); // Para mostrar los últimos 50 registros por defecto
    $app->get('/api/log/{lines}', [$loggerController, 'get']); // Para especificar el número de líneas
};