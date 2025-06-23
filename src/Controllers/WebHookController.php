<?php
namespace AppPHP\RedPay\Controllers;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response as ReactResponse;   // Importar React\Http\Message\Response
use AppPHP\RedPay\Repositories\WebhookRepository;
use AppPHP\RedPay\Models\WebhookModel;
use AppPHP\RedPay\Services\PagoService;
use AppPHP\RedPay\Models\ResponseModel;
use Monolog\Logger;
class WebHookController
{
    private WebhookRepository $webhookRepo;
    private PagoService $pagoService;
    private Logger $logger;
    public function __construct(WebhookRepository $webhookRepo, PagoService $pagoService, Logger $logger)
    {
        $this->pagoService = $pagoService;
        $this->webhookRepo = $webhookRepo;
        $this->logger = $logger;
    }
    public function redpay(ServerRequestInterface $request): ReactResponse
{
    $id = null;
    $requestPayload = [];
    $transactionNumber = null;
    $method = $request->getMethod();
    $this->logger->info("Webhook recibido: Método: $method, URI: " . $request->getUri());
    try {
        $id = (int) $request->getAttribute('id');
        $myApiResultData = null;
        if ($method === 'POST') {
            $body = (string) $request->getBody();
            $contentType = $request->getHeaderLine('Content-Type');
            $this->logger->info('Cuerpo del POST recibido: ' . $body);
            $this->logger->info('Tipo de contenido del POST: ' . $contentType);
            if (stripos($contentType, 'application/json') !== false) {
                $this->logger->info('Procesando cuerpo como JSON.');
                $requestPayload = json_decode($body, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Error al decodificar el cuerpo JSON: ' . json_last_error_msg());
                }
            } elseif (stripos($contentType, 'application/x-www-form-urlencoded') !== false) {
                $this->logger->info('Procesando cuerpo como formulario URL-encoded.');
                parse_str($body, $requestPayload);
                if (empty($requestPayload)) {
                    $this->logger->error('Error al decodificar el cuerpo URL-encoded: ' . $body);
                    throw new \Exception('Error al decodificar el cuerpo URL-encoded.');
                }
            } else {
                $this->logger->error('Tipo de contenido no soportado: ' . $contentType);
                throw new \Exception("Tipo de contenido no soportado: $contentType");
            }
        } else { // GET
            $requestPayload = $request->getQueryParams();
            $this->logger->info('Parámetros GET recibidos: ' . json_encode($requestPayload));
            if (isset($requestPayload['id'])) {
                $id = (int) $requestPayload['id'];
                $myResult = $this->pagoService->GetPayFromApi($id);
                $myApiResultData = isset($myResult->data) ? (array) $myResult->data : null;
            } else {
                return new ReactResponse(400, ['Content-Type' => 'application/json'], json_encode([
                    'status' => 'error',
                    'message' => 'El parámetro "id" es requerido para solicitudes GET.'
                ]));
            }
        }
        $normalizedRequestPayload = is_array($requestPayload)
            ? array_change_key_case($requestPayload, CASE_LOWER)
            : [];
        $this->logger->info('Carga útil normalizada del webhook: ' . json_encode($normalizedRequestPayload));
        $normalizedMyApiResultData = is_array($myApiResultData)
            ? array_change_key_case($myApiResultData, CASE_LOWER)
            : null;
        $this->logger->info('Datos normalizados de la API: ' . json_encode($normalizedMyApiResultData));
        if (!empty($normalizedRequestPayload['transactionnumber'])) {
            $this->logger->info('Número de transacción encontrado en la carga útil del webhook.'. $normalizedRequestPayload['transactionnumber']);
            $transactionNumber = $normalizedRequestPayload['transactionnumber'];
        } elseif (!empty($normalizedMyApiResultData['transactionnumber'])) {
            $this->logger->info('Número de transacción encontrado en los datos de la API.');
            $transactionNumber = $normalizedMyApiResultData['transactionnumber'];
        } else {
            $this->logger->info('Número de transacción no encontrado en la carga útil o datos de la API, usando ID.');
            $transactionNumber = $id;
        }
        $this->logger->info('Número de transacción determinado: ' . $transactionNumber);
        $webhookitem = new WebhookModel(
            $transactionNumber,
            json_encode($normalizedRequestPayload),
            $method,
            date('Y-m-d H:i:s')
        );
        $this->logger->info('Creando registro de webhook: ' . json_encode($webhookitem));
        $myResult = $this->webhookRepo->create($webhookitem);
        $this->logger->info('Result de creación.'. json_encode($myResult));
        
        $viewPath = __DIR__ . '/../views/webhook.php';
        ob_start();
        $dataForView = $normalizedRequestPayload;
        $apiResultForView = $normalizedMyApiResultData;
        $methodForView = $method;
        include $viewPath;
        $html = ob_get_clean();
        return new ReactResponse(200, ['Content-Type' => 'text/html'], $html);
    } catch (\Exception $e) {
        $this->logger->error('Error procesando el webhook: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'id' => $id,
            'requestPayload' => $requestPayload,
            'transactionNumber' => $transactionNumber
        ]);
        return new ReactResponse(500, ['Content-Type' => 'application/json'], json_encode([
            'status' => 'error',
            'message' => 'Ocurrió un error al procesar el webhook: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]));
    }
}
    public function get(ServerRequestInterface $request): ReactResponse
    {
        try {
            // Obtener los logs como objetos WebhookModel
            $logs = $this->webhookRepo->getAllWebhooks(false);
            // Deserializar los datos si están en formato serializado
            // Ahora los datos estarán disponibles como objetos o arreglos
            return new ReactResponse(200, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'success',
                'data' => $logs
            ]));
        } catch (\Exception $e) {
            // Registrar el error en el logger
            $this->logger->error('Error al obtener los logs: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return new ReactResponse(500, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'error',
                'message' => 'Ocurrió un error al obtener los logs: ' . $e->getMessage()
            ]));
        }
    }
    public function getLogById(ServerRequestInterface $request): ReactResponse
    {
        try {
            // Obtener el parámetro 'id' de la URL
            $idLog = (int) $request->getAttribute('id');
            // Obtener los logs como objetos WebhookModel
            $logs = $this->webhookRepo->getLogById($idLog);
            // Ahora los datos estarán disponibles como objetos o arreglos
            return new ReactResponse(200, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'success',
                'data' => $logs
            ]));
        } catch (\Exception $e) {
            // Registrar el error en el logger
            $this->logger->error('Error al obtener los logs por ID: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $request->getAttribute('id')
            ]);
            return new ReactResponse(500, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'error',
                'message' => 'Ocurrió un error al obtener los logs: ' . $e->getMessage()
            ]));
        }
    }
}