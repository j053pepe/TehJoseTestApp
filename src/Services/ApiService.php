<?php

namespace AppPHP\RedPay\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use AppPHP\RedPay\Models\ApiHostModel;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Promise\Promise;
use AppPHP\RedPay\Services\AppLoggerService;
use AppPHP\RedPay\Enums\LoggType;

class ApiService
{
    private Client $httpClient;
    private AppLoggerService $logger;

    public function __construct(Client $httpClient, AppLoggerService $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    public function SendPayToApi(ApiHostModel $settings, array $data = []): PromiseInterface
    {
        // Configurar los encabezados
        $headers = [];
        if (!empty($settings->base64Credentials)) {
            $this->logger->Create(LoggType::info, "ApiService - Configurando autenticación básica para la API.");
            $headers['Authorization'] = 'Basic ' . $settings->base64Credentials;
        }

        // Configurar la solicitud según la acción
        $options = ['headers' => $headers];
        if (in_array(strtoupper($settings->acction), ['POST', 'PUT', 'PATCH'])) {
            $this->logger->Create(LoggType::info,"ApiService - Configurando datos para la acción: ", strtoupper($settings->acction));
            $options['json'] = $data; // Datos como cuerpo JSON
        } elseif (strtoupper($settings->acction) === 'GET') {
            $this->logger->Create(LoggType::info,"ApiService - Configurando parámetros de consulta para la acción GET.");
            $options['query'] = $data; // Datos como parámetros de consulta
        }

        // Realizar la solicitud y retornar la promesa de Guzzle
        try {
            return match (strtoupper($settings->acction)) {
                'GET' => $this->httpClient->getAsync($settings->url, $options),
                'POST' => $this->httpClient->postAsync($settings->url, $options),
                'PUT' => $this->httpClient->putAsync($settings->url, $options),
                'PATCH' => $this->httpClient->patchAsync($settings->url, $options),
                'DELETE' => $this->httpClient->deleteAsync($settings->url, $options),
                default => throw new \InvalidArgumentException("Acción no soportada: " . $settings->acction),
            };
        } catch (\InvalidArgumentException $e) {
            $this->logger->Create(LoggType::critical,"ApiService - Error en la acción: " . $e->getMessage());
            // Manejar la excepción de acción no soportada
            return \GuzzleHttp\Promise\rejection_for('Error: ' . $e->getMessage());
        }
    }
}