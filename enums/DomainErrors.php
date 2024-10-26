<?php

namespace app\enums;

class DomainErrors
{
    const CUSTOM_ERROR_CODE = 9999;

    /**
     * Wallet errors
     */
    const WALLET_MISSING = [
        'code' => 1000,
        'message' => 'Wallet missing',
    ];

    const INCORRECT_BALANCE = [
        'code' => 1001,
        'message' => 'Incorrect balance'
    ];

    const INSUFFICIENT_FUND = [
        'code' => 1002,
        'message' => 'Insufficient funds'
    ];

    /**
     * Moment errors
     */
    const MOMENT_MISSING = [
        'code' => 2000,
        'message' => 'Moment missing',
    ];

    const MOMENT_NOT_SPECIFIED = [
        'code' => 2001,
        'message' => 'Moment not specified',
    ];

    const INCORRECT_TIMESTAMP = [
        'code' => 2002,
        'message' => 'Incorrect timestamp'
    ];

    /**
     * Order errors
     */
    const ORDER_NOT_FOUND = [
        'code' => 3000,
        'message' => 'Order not found',
    ];
}