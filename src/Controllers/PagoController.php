<?php

namespace AppPHP\RedPay\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface as Response;
use React\Http\Message\Response as ReactResponse;
use GuzzleHttp\Promise\PromiseInterface;

use AppPHP\RedPay\Services\PagoService;
use AppPHP\RedPay\Models\ResponseModel;
use AppPHP\RedPay\Services\AppLoggerService;
use AppPHP\RedPay\Enums\LoggType;

class PagoController
{
    private PagoService $pagoService;
    private AppLoggerService $logger;

    public function __construct(PagoService $pagoService, AppLoggerService $logger)
    {
        $this->logger = $logger;
        $this->pagoService = $pagoService;
    }

    public function Create(ServerRequestInterface $request): Response
    {
        try {
            // Obtener el ID del usuario desde los atributos del request
            $IdUsuario = $request->getAttribute('user_id');
            $this->logger->Create(LoggType::info, "PagoController - Create por Usuario: {$IdUsuario}", ['request' => $request]);

            if (!$IdUsuario) {
                $this->logger->Create(LoggType::error,"PagoController - Usuario no autenticado.", ['request' => $request]);
                throw new \Exception("Usuario no autenticado.");
            }

            $body = (string) $request->getBody();
            $data = json_decode($body, true);

            $resultService = $this->pagoService->CreatePay($data, $IdUsuario);

            if ($resultService->status === 'error') {
                $this->logger->Create(LoggType::error, "PagoController - Error al crear el pago.", ['result' => $resultService]);
                return new ReactResponse(400, ['Content-Type' => 'application/json'], json_encode($resultService));
            }

            return new ReactResponse(200, ['Content-Type' => 'application/json'], json_encode($resultService));

        } catch (\Exception $e) {
            $this->logger->Create(LoggType::error, "PagoController - Error en CreatePay: " . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return new ReactResponse(500, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'error',
                'message' => 'Error inesperado en el servicio de pago: ' . $e->getMessage()
            ]));
        }
    }

    public function GetAll(ServerRequestInterface $request): Response
    {
        try {            
            $IdUsuario = $request->getAttribute('user_id');
            $this->logger->Create(LoggType::info, "PagoController - GetAll por Usuario: {$IdUsuario}", ['request' => $request]);
            $resultService = $this->pagoService->GetAllPay();

            if ($resultService->status === 'error') {
                $this->logger->Create(LoggType::error, "PagoController - Error al obtener todos los pagos.", ['result' => $resultService]);
                return new ReactResponse(400, ['Content-Type' => 'application/json'], json_encode($resultService));
            }

            // Si todo salió bien, devolver un Response con el éxito
            return new ReactResponse(200, ['Content-Type' => 'application/json'], json_encode($resultService));

        } catch (\Exception $e) {
            $this->logger->Create(LoggType::critical, "PagoController - Exception: " . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return new ReactResponse(500, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'error',
                'message' => 'Error inesperado en el servicio de pago: ' . $e->getMessage()
            ]));
        }
    }

    public function GetByReference(ServerRequestInterface $request): Response
    {
        try {
            $IdUsuario = $request->getAttribute('user_id');
            $this->logger->Create(LoggType::info, "PagoController - GetByReference por Usuario: {$IdUsuario}", ['request' => $request]);
            
            $reference = $request->getAttribute('reference');
            if (!$reference) {
                $this->logger->Create(LoggType::error, "PagoController - Referencia no proporcionada.", ['request' => $request]);
                throw new \Exception("Referencia no proporcionada.");
            }
            $resultService = $this->pagoService->FindReference($reference);

            return new ReactResponse(200, ['Content-Type' => 'application/json'], json_encode($resultService));

        } catch (\Exception $e) {
            $this->logger->Create(LoggType::critical, "PagoController - Exception: " . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return new ReactResponse(500, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'error',
                'message' => 'Error inesperado en el servicio de pago: ' . $e->getMessage()
            ]));
        }
    }
}