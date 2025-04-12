<?php
namespace AppPHP\RedPay\Enums;

enum ApiType: string {
    case ApiPay = 'ApiPay';
    case ApiCreate = 'ApiCreate';
    case ApiResponse = 'ApiResponse';
}