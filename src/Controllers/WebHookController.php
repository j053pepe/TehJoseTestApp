<?php
namespace AppPHP\RedPay\Controllers;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response as ReactResponse;   // Importar React\Http\Message\Response
use AppPHP\RedPay\Repositories\WebhookRepository;
use AppPHP\RedPay\Models\WebhookModel;
use AppPHP\RedPay\Services\PagoService;
use AppPHP\RedPay\Models\ResponseModel;
class WebHookController
{
    private WebhookRepository $webhookRepo;
    private PagoService $pagoService;
    public function __construct(WebhookRepository $webhookRepo, PagoService $pagoService)
    {
        $this->pagoService = $pagoService;
        $this->webhookRepo = $webhookRepo;
    }
    public function redpay(ServerRequestInterface $request): ReactResponse
    {
        $id = null; // Inicializar $id para evitar warnings si no se define
        $requestPayload = []; // Renombrada para mayor claridad: contendrá los datos del request (GET query params o POST JSON body)
        $transactionNumber = null;
        $method = $request->getMethod();

        error_log('Método: ' . $method);

        try {
            // Intentar obtener el ID de los parámetros de la ruta (si la ruta lo define, ej. /api/webhook/redpay/{id})
            // FrameworkX usa getAttribute para parámetros de ruta.
            $id = (int) $request->getAttribute('id'); 

            $myApiResultData = null; // Variable para almacenar la respuesta de la API (solo para GET)

            if ($method === 'POST') {
                $body = (string) $request->getBody();
                
                // *** CORRECCIÓN: Usar json_decode() para obtener el array PHP del cuerpo JSON ***
                $requestPayload = json_decode($body, true); 

                // Verificar si la decodificación JSON fue exitosa
                if (json_last_error() !== JSON_ERROR_NONE) {
                    // Si hay un error en el JSON del cuerpo, lanzamos una excepción
                    throw new \Exception('Error al decodificar el cuerpo JSON: ' . json_last_error_msg());
                }

                error_log("Cuerpo decodificado (POST):\n" . print_r($requestPayload, true));

            } else { // Método GET
                $requestPayload = $request->getQueryParams(); // Obtener parámetros de la URL para GET
                error_log("Parámetros de consulta (GET):\n" . print_r($requestPayload, true));

                // Verificar si 'id' existe en los parámetros de consulta antes de acceder a él
                if (isset($requestPayload['id'])) {
                    $id = (int) $requestPayload['id']; // Sobrescribir $id si viene en los query params
                    $myResult = $this->pagoService->GetPayFromApi($id);
                    // Si GetPayFromApi retorna un objeto con una propiedad 'data', lo almacenamos.
                    if (isset($myResult->data)) {
                        $myApiResultData = (array) $myResult->data; // Almacenar los datos de la API en una variable separada
                    }
                } else {
                    // Si el parámetro 'id' no se encuentra en GET, responder con un error 400 Bad Request
                    error_log("Advertencia: El parámetro 'id' es requerido para solicitudes GET y no se encontró.");
                    return new ReactResponse(400, ['Content-Type' => 'application/json'], json_encode([
                        'status' => 'error',
                        'message' => 'El parámetro "id" es requerido para solicitudes GET.'
                    ]));
                }
            }

            // Normalizar las claves de los payloads a minúsculas para una búsqueda insensible a mayúsculas/minúsculas
            $normalizedRequestPayload = array_change_key_case($requestPayload, CASE_LOWER);
            $normalizedMyApiResultData = ($myApiResultData !== null) ? array_change_key_case($myApiResultData, CASE_LOWER) : null;

            // Determine the TransactionNumber robustly
            // Priority 1: Search for 'transactionnumber' in the normalized request payload
            if (array_key_exists('transactionnumber', $normalizedRequestPayload) && $normalizedRequestPayload['transactionnumber'] !== null) {
                $transactionNumber = $normalizedRequestPayload['transactionnumber'];
            } 
            // Priority 2: If not found in the payload, search in the normalized API data (only if GET and data exists)
            else if ($normalizedMyApiResultData !== null && array_key_exists('transactionnumber', $normalizedMyApiResultData) && $normalizedMyApiResultData['transactionnumber'] !== null) {
                $transactionNumber = $normalizedMyApiResultData['transactionnumber'];
            }
            // Priority 3: If none of the above, use the $id (which could come from the route or query params)
            else {
                $transactionNumber = $id; 
            }
            
            error_log("TransactionNumber: " . $transactionNumber);

            // Crear instancia del modelo con los datos recibidos
            // Se guarda el $requestPayload original (datos del request) como JSON string
            $webhookitem = new WebhookModel(
                $transactionNumber,
                json_encode($requestPayload), // Guardar el payload original del request como string JSON
                $method,
                date('Y-m-d H:i:s')
            );

            // Guardar en la base de datos
            $this->webhookRepo->create($webhookitem);

            // Obtener la ruta absoluta de la vista
            $viewPath = __DIR__ . '/../views/webhook.php';

            // Iniciar el buffer de salida para capturar el HTML
            ob_start();
            // Pasa las variables que tu vista webhook.php necesite.
            // Por ejemplo, si la vista necesita `$requestPayload` y `$myApiResultData`:
            $dataForView = $requestPayload; // Pasamos el payload original del request a la vista
            $apiResultForView = $myApiResultData; // Pasamos los datos de la API a la vista (será null para POST)
            $methodForView = $method;
            
            include $viewPath; // Incluye el archivo de la vista
            $html = ob_get_clean(); // Captura el HTML generado

            // Crear la respuesta con React\Http\Message\Response
            return new ReactResponse(
                200, // Código de estado HTTP: OK
                ['Content-Type' => 'text/html'], // Cabeceras de la respuesta
                $html // Cuerpo de la respuesta (el HTML capturado)
            );

        } catch (\Exception $e) {
            error_log('Error en el webhook: ' . $e->getMessage());
            // Retornar una respuesta de error en formato JSON
            return new ReactResponse(
                500, // Código de estado HTTP: Internal Server Error
                ['Content-Type' => 'application/json'],
                json_encode([
                    'status' => 'error',
                    'message' => 'Ocurrió un error al procesar el webhook: ' . $e->getMessage(),
                    // Agrega el trace para depuración; quítalo en producción por seguridad
                    'trace' => $e->getTraceAsString() 
                ])
            );
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
            return new ReactResponse(500, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'error',
                'message' => 'Ocurrió un error al obtener los logs: ' . $e->getMessage()
            ]));
        }
    }
}