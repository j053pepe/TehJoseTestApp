<?php
namespace AppPHP\RedPay\Models;

class CompanyModel {
    public ?int $IdCompany;
    public string $CompanyName;
    public string $ApiKey;
    public string $Password;
    public string $Host;
    public string $ApiJsonUrl;

    public function __construct(
        int $IdCompany,
        string $CompanyName,
        string $ApiKey,
        string $Password,
        string $Host,
        string $ApiJsonUrl
    ){
        $this->IdCompany=$IdCompany;
        $this->CompanyName=$CompanyName;
        $this->ApiKey=$ApiKey;
        $this->Password=$Password;
        $this->Host=$Host;
        $this->ApiJsonUrl=$ApiJsonUrl;
    }
}