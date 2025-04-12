<?php

namespace AppPHP\RedPay\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use AppPHP\RedPay\Repositories\PaisRepository;

class PaisController {
    private PaisRepository $PaisRepo;
    public function __construct(PaisRepository $repo){
        $this->PaisRepo=$repo;
    }

    public function GetAll(ServerRequestInterface $request): Response
    {
        try {
            // Obtener los logs como objetos WebhookModel
            $paises = $this->PaisRepo->GetAll();
    
            // Deserializar los datos si están en formato serializado

            // Ahora los datos estarán disponibles como objetos o arreglos
            return new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'success',
                'data' => $paises
            ]));
        } catch (\Exception $e) {
            return new Response(500, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'error',
                'message' => 'Ocurrió un error al obtener los logs: ' . $e->getMessage()
            ]));
        }
    }

    public function GetByCode(ServerRequestInterface $request): Response
    {
        try {
             // Obtener el parámetro 'id' de la URL
             $code = (int) $request->getAttribute('code');

            // Obtener los logs como objetos WebhookModel
            $pais = $this->PaisRepo->GetByCode($code);
    
            // Deserializar los datos si están en formato serializado

            // Ahora los datos estarán disponibles como objetos o arreglos
            return new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'success',
                'data' => $paises
            ]));
        } catch (\Exception $e) {
            return new Response(500, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'error',
                'message' => 'Ocurrió un error al obtener los logs: ' . $e->getMessage()
            ]));
        }
    }
}