<?php
namespace AppPHP\RedPay\Models;

class PagoModel {
    public ?int $IdPago;
    public ?string $IdUsuario;
    public array $PayData;
    public string $Referencia;
    public float $Monto;
    public string $CreateDate;
    public string $Estatus;
    public ?string $Username; // Cambiado a ?string para permitir null
    public ?string $Email;    // Cambiado a ?string para permitir null

    public function __construct(        
        string|array $PayData, // Aceptar tanto string (JSON) como array
        string $Referencia,
        float $Monto,
        string $CreateDate,
        string $Estatus,
        ?string $Username,
        ?string $Email,
        ?int $IdPago = null,
        ?string $IdUsuario = null
    ){
        // Si $PayData es un array, se guarda como array
        if($PayData){
            if (is_array($PayData)) {
                $this->PayData = $PayData;
            } else {
                // Si $PayData no es un array, lo tratamos como string (JSON)
                $this->PayData = $PayData ? json_decode($PayData, true) : [];
            }
        }

        $this->Referencia = $Referencia;
        $this->Monto = $Monto;
        $this->CreateDate = $CreateDate;
        $this->Estatus = $Estatus;
        $this->IdUsuario = $IdUsuario;
        $this->IdPago = $IdPago;
        $this->Username = $Username;
        $this->Email = $Email;
    }
        public function toArray(): array {
        return [
            'IdPago' => $this->IdPago,
            'Referencia' => $this->Referencia,
            'Monto' => $this->Monto,
            'CreateDate' => $this->CreateDate,
            'Estatus' => $this->Estatus,
            'IdUsuario' => $this->IdUsuario,
            'Username' => $this->Username,
            'Email' => $this->Email,
            'PayData' => $this->PayData, // Asegúrate de que PayData sea un array
        ];
    }
}