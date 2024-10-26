<?php

namespace app\enums;

class DBTables
{
    /**
     * Аккаунты
     */
    const ACCOUNT = 'account';

    /**
     * Активы
     */
    const ASSET = 'asset';

    /**
     * Активы, которые сняты с торгов на бирже
     */
    const DELISTED_ASSET  = 'delisted_asset';
    
    /**
     * Метки момента времени
     */
    const MOMENT = 'moment';

    /**
     * Ордера
     */
    const ORDER = 'order';

    /**
     * Валютные пары
     */
    const PAIR = 'pair';

    /**
     * Комиссии на операции в валютных парах
     */
    const PAIR_COMMISSION = 'pair_commission';

    /**
     * Конфигурации валютных пар
     */
    const PAIR_CONFIGURATION = 'pair_configuration';

    /**
     * Курсы валют
     */
    const RATE = 'rate';

    /**
     * Транзакции кошелька
     */
    const TRANSACTION = 'transaction';

    /**
     * Кошельки
     */
    const WALLET = 'wallet';
}