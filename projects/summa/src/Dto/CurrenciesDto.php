<?php

namespace App\Dto;

use DateTime;

class CurrenciesDto
{
    public ResultCurrenciesDto $response;
    public string $status;
}

class ResultCurrenciesDto
{
    public DataCurrenciesDto $result;
}

class DataCurrenciesDto
{
    public string $from;
    public ConversionDTO|array $conversion;

}

class ConversionDTO
{
    public string $to;
    public DateTime $date;
    public float $rate;
}