<?php
namespace AppPHP\RedPay\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response as ReactResponse;   // Importar React\Http\Message\Response
use AppPHP\RedPay\Repositories\WebhookRepository;
use AppPHP\RedPay\Models\WebhookModel;
use AppPHP\RedPay\Services\PagoService;
use AppPHP\RedPay\Models\ResponseModel;
use AppPHP\RedPay\Services\AppLoggerService;
use AppPHP\RedPay\Enums\LoggType;

class WebHookController
{
    private WebhookRepository $webhookRepo;
    private PagoService $pagoService;
    private AppLoggerService $logger;
    public function __construct(WebhookRepository $webhookRepo, PagoService $pagoService, AppLoggerService $logger)
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
    $this->logger->Create(LoggType::info, 'WebHookController - Iniciando procesamiento del webhook.', [
        'method' => $method,
        'uri' => (string) $request->getUri(),
        'headers' => $request->getHeaders()
    ]);
    try {
        $id = (int) $request->getAttribute('id');
        $myApiResultData = null;
        if ($method === 'POST') {
            $body = (string) $request->getBody();
            $contentType = $request->getHeaderLine('Content-Type');
            if (stripos($contentType, 'application/json') !== false) {
                $requestPayload = json_decode($body, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->logger->Create(LoggType::error, 'WebHookController - Error al decodificar el cuerpo JSON.', [
                        'error' => json_last_error_msg(),
                        'body' => $body
                    ]);
                    throw new \Exception('Error al decodificar el cuerpo JSON: ' . json_last_error_msg());
                }
            } elseif (stripos($contentType, 'application/x-www-form-urlencoded') !== false) {
                parse_str($body, $requestPayload);
                if (empty($requestPayload)) {
                    $this->logger->Create(LoggType::error, 'WebHookController - Error al decodificar el cuerpo URL-encoded.', [
                        'body' => $body
                    ]);
                    throw new \Exception('Error al decodificar el cuerpo URL-encoded.');
                }
            } else {
                $this->logger->Create(LoggType::error, 'WebHookController - Tipo de contenido no soportado.', [
                    'contentType' => $contentType
                ]);
                throw new \Exception("Tipo de contenido no soportado: $contentType");
            }
        } else { // GET
            $requestPayload = $request->getQueryParams();
            if (isset($requestPayload['id'])) {
                $id = (int) $requestPayload['id'];
                $myResult = $this->pagoService->GetPayFromApi($id);
                $myApiResultData = isset($myResult->data) ? (array) $myResult->data : null;
            } else {
                $this->logger->Create(LoggType::error, 'WebHookController - ID requerido para solicitudes GET.', [
                    'requestPayload' => $requestPayload
                ]);
                return new ReactResponse(400, ['Content-Type' => 'application/json'], json_encode([
                    'status' => 'error',
                    'message' => 'El parámetro "id" es requerido para solicitudes GET.'
                ]));
            }
        }
        $normalizedRequestPayload = is_array($requestPayload)
            ? array_change_key_case($requestPayload, CASE_LOWER)
            : [];
            
        $normalizedMyApiResultData = is_array($myApiResultData)
            ? array_change_key_case($myApiResultData, CASE_LOWER)
            : null;
            
        if (!empty($normalizedRequestPayload['transactionnumber'])) {
            $transactionNumber = $normalizedRequestPayload['transactionnumber'];
        } elseif (!empty($normalizedMyApiResultData['transactionnumber'])) {
            $transactionNumber = $normalizedMyApiResultData['transactionnumber'];
        } else {
            $transactionNumber = $id;
        }
        $webhookitem = new WebhookModel(
            $transactionNumber,$method === 'POST' ? json_encode($normalizedRequestPayload) : json_encode($normalizedMyApiResultData),
            $method,
            date('Y-m-d H:i:s')
        );
        $myResult = $this->webhookRepo->create($webhookitem);
        
        $viewPath = __DIR__ . '/../views/webhook.php';
        ob_start();
        $dataForView = $myResult;// json_decode(json_encode($myResult), true); // fuerza conversión a array
        //$apiResultForView = $myResult;
        $methodForView = $method;
        include $viewPath;
        $html = ob_get_clean();
        return new ReactResponse(200, ['Content-Type' => 'text/html'], $html);
    } catch (\Exception $e) {
        // Registrar el error en el logger
        $this->logger->Create(LoggType::critical, 'WebHookController - Error al procesar el webhook.', [
            'message' => $e->getMessage(),
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
            $logs = $this->webhookRepo->getAllWebhooks(false);
            return new ReactResponse(200, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'success',
                'data' => $logs
            ]));
        } catch (\Exception $e) {
            $this->logger->Create(LoggType::critical, 'WebHookController - Error al obtener los logs.', [
                'message' => $e->getMessage(),
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
            $idLog = (int) $request->getAttribute('id');
            $logs = $this->webhookRepo->getLogById($idLog);
            return new ReactResponse(200, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'success',
                'data' => $logs
            ]));
        } catch (\Exception $e) {
            $this->logger->Create(LoggType::critical, 'WebHookController - Error al obtener el log por ID.', [
                'id' => $idLog,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return new ReactResponse(500, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'error',
                'message' => 'Ocurrió un error al obtener los logs: ' . $e->getMessage()
            ]));
        }
    }
}