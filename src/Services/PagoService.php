<?php

namespace AppPHP\RedPay\Services;

use AppPHP\RedPay\Repositories\PagoRepository;
use AppPHP\RedPay\Repositories\CompanyRepository;
use AppPHP\RedPay\Models\PagoModel;
use AppPHP\RedPay\Models\CompanyModel;
use AppPHP\RedPay\Models\ApiHostModel;
use AppPHP\RedPay\Models\ResponseModel;
use AppPHP\RedPay\Enums\ApiType;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\RejectedPromise;

class PagoService
{
    private PagoRepository $pagaRepo;
    private ApiService $apiEnvioService;
    private CompanyRepository $companyRepo;
    private int $idCompany;

    public function __construct(PagoRepository $pagaRepo, CompanyRepository $companyRepo, ApiService $apiEnvioService)
    {
        $this->pagaRepo = $pagaRepo;
        $this->apiEnvioService = $apiEnvioService;
        $this->companyRepo = $companyRepo;
        $this->idCompany = 1;
    }

    public function CreatePay(array $data, string $IdUsuario): ResponseModel
    {
        try {
            //Validar que la referencia no exista ya 
            $pagoExistente = $this->pagaRepo->GetByReference($data["ReferenceNumber"]);
            if ($pagoExistente) {
                return new ResponseModel('error', 'La referencia ya existe en la base de datos.', $pagoExistente);
            }
            // Crear el objeto PagoModel
            $itemPago = new PagoModel(
                json_encode($data ?? []),
                $data["ReferenceNumber"],
                (float)$data["Amount"],
                date('Y-m-d H:i:s'),
                "Creado",
                null,
                null,
                null,
                $IdUsuario
            );

            // Guardar el pago
            $this->pagaRepo->create($itemPago);    

            // Obtener configuraciones de la compañía
            $settings = $this->GetSettings($this->idCompany,ApiType::ApiPay);

            // Si no se encuentran los datos de la compañía, retornar un ResponseModel con error
            if ($settings === null) {
                return new ResponseModel('error', 'No se encontraron los datos de la compañía');
            }

            // Actualizar el estado del pago a "Pendiente"
            $this->pagaRepo->updateStatus($itemPago->IdPago, "Pendiente");

            // Enviar el pago a la API y manejar la promesa
            $result = $this->handleApiResponse($settings, $data, $itemPago);
            
            return $result; // Retornar el resultado de la promesa
        } catch (\Exception $e) {   
            // Si ocurre un error, loguearlo y retornar un ResponseModel con error
            error_log("Error en CreatePay: " . $e->getMessage() . " en " . $e->getFile() . " línea " . $e->getLine());
    
            return new ResponseModel('error', 'Error general: ' . $e->getMessage());
        }
    }

    public function GetPayFromApi($id):ResponseModel
    {
        try {
            
            // Obtener configuraciones de la compañía
            $settings = $this->GetSettings($this->idCompany, ApiType::ApiResponse);
            // Si no se encuentran los datos de la compañía, retornar un ResponseModel con error
            if ($settings === null) {
                return new ResponseModel('error', 'No se encontraron los datos de la compañía');
            }
            else {
                $settings->url = $settings->url . $id;
            }
            // Enviar cada pago a la API y manejar la promesa
            $result = $this->handleApiResponse($settings, [], null);

            return $result; // Retornar el resultado de la promesa
        } catch (\Exception $e) {
            // Manejar errores de la base de datos
            error_log("Error al obtener pagos: " . $e->getMessage());
            return new ResponseModel("error", "Error al obtener pagos: " . $e->getMessage());
        }
    }

    public function GetAllPay(): ResponseModel
    {
        try {
            // Obtener todos los pagos
            $pagos = $this->pagaRepo->GetAllPay();
            if($pagos === null) {
                return new ResponseModel("error", "No se encontraron pagos.");
            } else {
                return new ResponseModel("success", "Pagos obtenidos con éxito.", $pagos);
            }
        } catch (\Exception $e) {
            // Manejar errores de la base de datos
            error_log("Error al obtener pagos: " . $e->getMessage());
            return new ResponseModel("error", "Error al obtener pagos: " . $e->getMessage());
        }
    }

    public function FindReference(string $reference): ResponseModel
    {
        try {
            // Buscar el pago por referencia
            $pago = $this->pagaRepo->GetByReference($reference);
            if ($pago) {
                return new ResponseModel("error", "Referencia usada previamente.");
            } else {
                return new ResponseModel("success", "Se puede usar la referencia.");
            }
        } catch (\Exception $e) {
            // Manejar errores de la base de datos
            error_log("Error al buscar pago: " . $e->getMessage());
            return new ResponseModel("error", "Error al buscar pago: " . $e->getMessage());
        }
    }

    private function determinarEstadoPago(array $apiResponse): string
    {
        // Implementa la lógica para determinar el estado del pago
        // basado en la respuesta de la API externa.
        // Ejemplo:
        if ($apiResponse['responseCode'] === "002") {
            return "Pagado";
        } elseif ($apiResponse['status'] === "003") {
            return "Rechazado";
        } elseif ($apiResponse['status'] === "007") {
            return "En Revisión";
        } else {
            return "Error"; // O algún otro estado por defecto
        }
    }

    private function GetSettings(int $idCompany, ApiType $type): ApiHostModel
    {
        $company = $this->companyRepo->GetCompanyById($idCompany);

        if ($company) {
            $apihost = new ApiHostModel($company, $type);
           return $apihost;            
        } else {
            return null;
        }
    }
    /**
     * Método auxiliar para manejar la promesa y devolver el Response adecuado
     */
    private function handleApiResponse($settings, $data, $itemPago=null): ResponseModel
    {
        try {
            $apiResponse = $this->apiEnvioService->SendPayToApi($settings, $data)->wait();
    
            // Decodificar la respuesta JSON a un array
            $responseData = json_decode($apiResponse->getBody()->getContents(), true);
    
            // Verificar si la decodificación fue exitosa
            if ($responseData === null && json_last_error() !== JSON_ERROR_NONE) {
                return new ResponseModel('error', 'Error al decodificar la respuesta JSON: ' . json_last_error_msg(), $responseData);
            }
    
            $estadoPago = $this->determinarEstadoPago($responseData);
            if($itemPago != null){
                $this->pagaRepo->updateStatusResponse($itemPago->IdPago, $estadoPago, $responseData);
            }            
    
            return new ResponseModel(
                $estadoPago == "Pagado" || $estadoPago == "En Revisión" ? "success" : "error",
                $estadoPago == "Pagado" || $estadoPago == "En Revisión" ?"" : $responseData['message']??'',
                $responseData
            );
        } catch (GuzzleException $e) {
            // Manejar errores de Guzzle
            return new ResponseModel('error', 'Error al enviar datos a la API externa: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Manejar otros errores
            return new ResponseModel('error', 'Error desconocido: ' . $e->getMessage());
        }
    }
}