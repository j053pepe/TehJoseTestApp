<?php

namespace AppPHP\RedPay\Models;

class RedPayRequestModel
{
    public string $ReferenceNumber;
    public float $Amount;
    public string $Currency;
    public string $FirstName;
    public string $LastName;
    public string $Email;
    public string $PhoneNumber;
    public string $Street;
    public ?string $StreetNumber; // Puede ser null
    public ?string $StreetNumber2; // Puede ser null
    public string $Street2Col;
    public ?string $Street2Del; // Puede ser null
    public string $City;
    public string $State;
    public int $IdCountry;
    public string $Country;
    public string $PostalCode;
    public string $cardNumber;
    public string $cardExpirationMonth;
    public string $cardExpirationYear;
    public string $cvv;

    // Constructor opcional para inicializar las propiedades
    public function __construct(array $data = [])
    {
        $this->ReferenceNumber = $data['ReferenceNumber'] ?? '';
        $this->Amount = $data['Amount'] ?? 0.0;
        $this->Currency = $data['Currency'] ?? '';
        $this->FirstName = $data['FirstName'] ?? '';
        $this->LastName = $data['LastName'] ?? '';
        $this->Email = $data['Email'] ?? '';
        $this->PhoneNumber = $data['PhoneNumber'] ?? '';
        $this->Street = $data['Street'] ?? '';
        $this->StreetNumber = $data['StreetNumber'] ?? null;
        $this->StreetNumber2 = $data['StreetNumber2'] ?? null;
        $this->Street2Col = $data['Street2Col'] ?? '';
        $this->Street2Del = $data['Street2Del'] ?? null;
        $this->City = $data['City'] ?? '';
        $this->State = $data['State'] ?? '';
        $this->IdCountry = $data['IdCountry'] ?? 0;
        $this->Country = $data['Country'] ?? '';
        $this->PostalCode = $data['PostalCode'] ?? '';
        $this->cardNumber = $data['cardNumber'] ?? '';
        $this->cardExpirationMonth = $data['cardExpirationMonth'] ?? '';
        $this->cardExpirationYear = $data['cardExpirationYear'] ?? '';
        $this->cvv = $data['cvv'] ?? '';
    }
}
