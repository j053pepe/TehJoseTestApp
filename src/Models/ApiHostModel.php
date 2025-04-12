<?php
namespace AppPHP\RedPay\Models;

use AppPHP\RedPay\Models\CompanyModel;
use AppPHP\RedPay\Enums\ApiType;

class ApiHostModel {
    public string $url;
    public string $base64Credentials;
    public string $acction; // Propiedad corregida

    public function __construct(CompanyModel $company, ApiType $apiType) {
        $urlBase = $company->Host;
        $credentials = $company->ApiKey . ':' . $company->Password;

        // Decodificar el JSON para obtener las rutas de las APIs
        $apiUrls = json_decode($company->ApiJsonUrl, true);

        // Obtener la URL correspondiente al tipo de API
        if (isset($apiUrls[$apiType->value])) {
            $this->url = $urlBase . $apiUrls[$apiType->value];
        } else {
            throw new \InvalidArgumentException("Tipo de API no válido: " . $apiType->value);
        }

        // Determinar la acción (GET o POST, por ejemplo)
        $this->acction = $apiType->value === 'ApiResponse' ? 'GET' : 'POST';

        $this->base64Credentials = base64_encode($credentials);
    }
}