<?php

use FrameworkX\App;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use AppPHP\RedPay\Settings\XContainerAdaptador;

return function (App $app, XContainerAdaptador $adaptador) {
    // Obtener las instancias de los controladores y middlewares desde el contenedor
    $accountController = $adaptador->get(AppPHP\RedPay\Controllers\AccountController::class);
    $webhookController = $adaptador->get(AppPHP\RedPay\Controllers\WebHookController::class);
    $messageController = $adaptador->get(AppPHP\RedPay\Controllers\MessageController::class);
    $pagoController = $adaptador->get(AppPHP\RedPay\Controllers\PagoController::class);
    $paisController = $adaptador->get(AppPHP\RedPay\Controllers\PaisController::class);
    $tokenMiddleware = $adaptador->get(AppPHP\RedPay\Middlewares\TokenMiddleware::class);

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
};