<?php

namespace App\Enum;

/**
 * Перечисление типов транзакций.
 */
enum TransactionStatus: string
{
    case DEPOSIT       = 'deposit';
    case WITHDRAW      = 'withdraw';
    case TRANSFER_IN   = 'transfer_in';
    case TRANSFER_OUT  = 'transfer_out';

    /**
     * Проверяет, является ли статус зачислением.
     */
    public function isCredit(): bool
    {
        return in_array($this, [self::DEPOSIT, self::TRANSFER_IN], true);
    }

    /**
     * Проверяет, является ли статус списанием.
     */
    public function isDebit(): bool
    {
        return in_array($this, [self::WITHDRAW, self::TRANSFER_OUT], true);
    }
}
