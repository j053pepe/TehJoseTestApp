<?php

namespace AppPHP\RedPay\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface as Response;
use React\Http\Message\Response as ReactResponse;
use GuzzleHttp\Promise\PromiseInterface;
use Monolog\Logger;

use AppPHP\RedPay\Services\PagoService;
use AppPHP\RedPay\Models\ResponseModel;
class PagoController
{
    private PagoService $pagoService;
    private Logger $logger;

    public function __construct(PagoService $pagoService, Logger $logger)
    {
        $this->logger = $logger;
        $this->pagoService = $pagoService;
    }

    public function Create(ServerRequestInterface $request): Response
    {
        try {
            // Obtener el ID del usuario desde los atributos del request
            $IdUsuario = $request->getAttribute('user_id');

            if (!$IdUsuario) {
                throw new \Exception("Usuario no autenticado.");
            }

            // Obtener los datos de la solicitud
            $body = (string) $request->getBody();
            $data = json_decode($body, true);

            // Llamar a CreatePay, que ahora retorna un ResponseModel directamente
            $resultService = $this->pagoService->CreatePay($data, $IdUsuario);

            // Si el status es 'error', devolver un Response con el error
            if ($resultService->status === 'error') {
                // Loguear el error si ocurre una excepción
                $this->logger->error("Error en CreatePay: " . $resultService->message, ['data' => $data]);
                return new ReactResponse(400, ['Content-Type' => 'application/json'], json_encode($resultService));
            }

            // Si todo salió bien, devolver un Response con el éxito
            return new ReactResponse(200, ['Content-Type' => 'application/json'], json_encode($resultService));

        } catch (\Exception $e) {
            $this->logger->error("Error en CreatePay: " . $e->getMessage(), ['exception' => $e]);
            // Loguear el error si ocurre una excepción
            error_log("Error en CreatePay: " . $e->getMessage() . " en " . $e->getFile() . " línea " . $e->getLine());
            error_log($e->getTraceAsString()); // Mostrar toda la pila de ejecución

            // Si ocurre un error general, devolver un Response con el mensaje de error
            return new ReactResponse(500, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'error',
                'message' => 'Error inesperado en el servicio de pago: ' . $e->getMessage()
            ]));
        }
    }

    public function GetAll(ServerRequestInterface $request): Response
    {
        try {
            $this->logger->info("Llamando a GetAllPay");
            // Llamar a GetAllPay
            $resultService = $this->pagoService->GetAllPay();

            // Si el status es 'error', devolver un Response con el error
            if ($resultService->status === 'error') {
                return new ReactResponse(400, ['Content-Type' => 'application/json'], json_encode($resultService));
            }

            // Si todo salió bien, devolver un Response con el éxito
            return new ReactResponse(200, ['Content-Type' => 'application/json'], json_encode($resultService));

        } catch (\Exception $e) {
            // Loguear el error si ocurre una excepción
            error_log("Error en GetAllPay: " . $e->getMessage() . " en " . $e->getFile() . " línea " . $e->getLine());
            error_log($e->getTraceAsString()); // Mostrar toda la pila de ejecución

            // Si ocurre un error general, devolver un Response con el mensaje de error
            return new ReactResponse(500, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'error',
                'message' => 'Error inesperado en el servicio de pago: ' . $e->getMessage()
            ]));
        }
    }

    public function GetByReference(ServerRequestInterface $request): Response
    {
        try {
            $reference = $request->getAttribute('reference');
            if (!$reference) {
                throw new \Exception("Referencia no proporcionada.");
            }
            // Llamar a GetByReference
            $resultService = $this->pagoService->FindReference($reference);

            // Si todo salió bien, devolver un Response con el éxito
            return new ReactResponse(200, ['Content-Type' => 'application/json'], json_encode($resultService));

        } catch (\Exception $e) {
            // Loguear el error si ocurre una excepción
            error_log("Error en GetByReference: " . $e->getMessage() . " en " . $e->getFile() . " línea " . $e->getLine());
            error_log($e->getTraceAsString()); // Mostrar toda la pila de ejecución

            // Si ocurre un error general, devolver un Response con el mensaje de error
            return new ReactResponse(500, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'error',
                'message' => 'Error inesperado en el servicio de pago: ' . $e->getMessage()
            ]));
        }
    }
}