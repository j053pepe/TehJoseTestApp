<?php

namespace AppPHP\RedPay\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use AppPHP\RedPay\Models\ApiHostModel;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Promise\Promise;

class ApiService
{
    private Client $httpClient;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function SendPayToApi(ApiHostModel $settings, array $data = []): PromiseInterface
    {
        // Configurar los encabezados
        $headers = [];
        if (!empty($settings->base64Credentials)) {
            $headers['Authorization'] = 'Basic ' . $settings->base64Credentials;
        }

        // Configurar la solicitud según la acción
        $options = ['headers' => $headers];
        if (in_array(strtoupper($settings->acction), ['POST', 'PUT', 'PATCH'])) {
            $options['json'] = $data; // Datos como cuerpo JSON
        } elseif (strtoupper($settings->acction) === 'GET') {
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
            // Manejar la excepción de acción no soportada
            return \GuzzleHttp\Promise\rejection_for('Error: ' . $e->getMessage());
        }
    }
}