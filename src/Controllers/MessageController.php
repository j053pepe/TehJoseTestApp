<?php
namespace AppPHP\RedPay\Controllers;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use AppPHP\RedPay\Repositories\WebhookRepository;

class MessageController{
    public function home(ServerRequestInterface $request)
    {
        return Response::plaintext(
            "Hello " . $request->getAttribute('name') . "!\n"
        );
    }
    public function Hello(ServerRequestInterface $request){
        try {
            $body = (string) $request->getBody();
            $data = json_decode($body, true);

            return new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'message' => 'Texto recibido',
                'status' => 'success',
                'data' => $data
            ]));
        
        } catch (\Exception $e) {
            return new Response(500, ['Content-Type' => 'application/json'], json_encode([
                'message' => 'Error interno del servidor: ' . $e->getMessage(),
                'status' => 'error'
            ]));
        }
    }
    public function GetAllWebhookReponse(ServerRequestInterface $request){

        $repoWebhook= new WebhookRepository();

        $data = $repoWebhook->getAllWebhooks();

        return new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'message' => 'Texto recibido',
            'status' => 'success',
            'data' => $data
        ]));
    }
}