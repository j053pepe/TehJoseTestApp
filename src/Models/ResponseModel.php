<?php
namespace AppPHP\RedPay\Models;

class ResponseModel {
    public string $status;
    public string $message;
    public ?array $data; // Ahora acepta array o null

    public function __construct(string $status, string $message, ?array $data = null) {
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
    }
}