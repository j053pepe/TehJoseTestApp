<?php
namespace AppPHP\RedPay\Enums;

enum LoggType: string {
    case error = 'error';
    case info = 'info';
    case debug = 'debug';
    case warning = 'warning';
    case critical = 'critical';
}