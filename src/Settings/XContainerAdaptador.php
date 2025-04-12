<?php

namespace AppPHP\RedPay\Settings;

use Psr\Container\ContainerInterface;
use FrameworkX\Container;
use FrameworkX\ErrorHandler;
use FrameworkX\AccessLogHandler; // Asegúrate de incluir este use

class XContainerAdaptador extends Container
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function get(string $id)
    {
        return $this->container->get($id);
    }

    public function has(string $id): bool
    {
        return $this->container->has($id);
    }

    public function loadObject(string $id)
    {
        return $this->get($id);
    }

    public function loadCallable(string $id)
    {
        return $this->get($id);
    }

    public function getErrorHandler(): ErrorHandler // Agregado el tipo de retorno
    {
        return $this->get('FrameworkX\ErrorHandler');
    }

    public function getAccessLogHandler(): AccessLogHandler // Corregido el tipo de retorno
    {
        return $this->get('FrameworkX\AccessLogHandler');
    }
}