<?php

namespace enums;

class ApiErrorCode
{
    const CUSTOM_ERROR_CODE = 9999;

    const BAD_REQUEST = [
        'code' => 1000,
        'message' => 'Некорректный запрос'
    ];

    const INSUFFICIENT_FUND = [
        'code' => 2000,
        'message' => 'Недостаточно средств'
    ];

    const UNAVAILABLE_CURRENCY_PAIR = [
        'code' => 2001,
        'message' => 'Торги в данной валютной паре недоступны'
    ];
}