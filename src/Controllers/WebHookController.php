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
        try {
            $method = $request->getMethod(); // Obtiene el método (GET o POST)
            $id=(int) $request->getAttribute('id');
            if ($method === 'POST') {
                $data = $request->getParsedBody(); // Obtener datos del cuerpo en POST
            } else {
                $data = $request->getQueryParams(); // Obtener parámetros en GET
                $id=(int) $data['id'];
                $myResult= $this->pagoService->GetPayFromApi($id);
            }

            if (array_key_exists('TransactionNumber', $data) && $data['TransactionNumber'] !== null) {
                $transactionNumber = $data['TransactionNumber'];
            } else {
                $transactionNumber = $id ?? null; // Obtener el transactionNumber de la respuesta de la API            
                $data = $myResult->data ?? null;
            }
        
            // Crear instancia del modelo con los datos recibidos
            $webhookitem = new WebhookModel(
                $transactionNumber,
                $data, // Guardamos el request en logWebhook
                $method,
                date('Y-m-d H:i:s')
            );
        
            // Guardar en la base de datos
            $this->webhookRepo->create($webhookitem);
        
            // Obtener la ruta absoluta de la vista
            $viewPath = __DIR__ . '/../views/webhook.php';
        
            // Iniciar el buffer de salida para capturar el HTML
            ob_start();
            include $viewPath;
            $html = ob_get_clean();
        
            // Crear la respuesta con React\Http\Message\Response
            return new ReactResponse(
                200, // Código de estado
                ['Content-Type' => 'text/html'], // Headers
                $html // Cuerpo de la respuesta (HTML)
            );
        } catch (\Exception $e) {
            return new ReactResponse(500, ['Content-Type' => 'application/json'], json_encode([
                'status' => 'error',
                'message' => 'Ocurrió un error al procesar el webhook: ' . $e->getMessage()
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