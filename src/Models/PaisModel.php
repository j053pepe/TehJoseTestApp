<?php
namespace AppPHP\RedPay\Models;

class PaisModel{
    public string $Name;
    public string $Code;

    public function __construct(string $name, string $code){
        $this->Name=$name;
        $this->Code=$code;
    }
}