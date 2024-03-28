<?php

namespace App\Dto;

use DateTime;

class FreeCurrencyDto
{
    public ResultDto $response;
    public string $status;
}

class ResultDto
{
    public DataDto $result;
}

class DataDto
{
    public DateTime $updated;
    public string $source;
    public string $target;
    public float $value;
    public float $quantity;
    public float $amount;
}