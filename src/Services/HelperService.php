<?php

namespace AppPHP\RedPay\Services;

class HelperService
{
    /**
     * Convierte una fecha de tipo DateTime a un string en formato 'Y-m-d H:i:s'.
     *
     * @param \DateTime $dateTime
     * @return string
     */
    public static function dateToString(string $dateutc): string
    {
        // Convierte la fecha UTC a un formato legible
        $date = new \DateTime($dateutc, new \DateTimeZone('UTC'));
        $date->setTimezone(new \DateTimeZone('America/Mexico_City')); // Cambia a tu zona horaria
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Convierte un string en formato 'Y-m-d H:i:s' a un objeto DateTime.
     *
     * @param string $dateString
     * @return \DateTime
     */
    public static function stringToDate(string $dateString): \DateTime
    {
        return new \DateTime($dateString);
    }
}