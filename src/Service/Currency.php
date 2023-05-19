<?php
declare(strict_types = 1);

namespace PayX\CommissionTask\Service;

class Currency
{
    public static function getCurrencyRates(): array
    {
        $json = file_get_contents('https://developers.paysera.com/tasks/api/currency-exchange-rates');
        $data = json_decode($json, true);

        return $data['rates'];
    }
}