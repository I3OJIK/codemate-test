<?php

namespace App\Services;

use App\DTOs\Requests\AccountTransactionDto;
use App\Enum\TransactionStatus as EnumTransactionStatus;
use App\Enums\TransactionStatus;
use App\Exceptions\InsufficientFundsException;
use App\Models\Transaction;
use App\Models\Balance;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BalanceService
{
    /**
     * Получение баланса пользователя
     * 
     * @param int $userId
     * 
     * @return float
     * @throws ModelNotFoundException
     */
    public function getBalance(int $userId): float
    {
        $balance = Balance::where('user_id', $userId)->first();

        if (!$balance){
            throw new ModelNotFoundException("Balance for the specified user not found");
        }

        return (float) $balance->amount;
    }

    /**
     * Обновление баланса пользователя
     * 
     * @param Balance $balance
     * @param float $amount
     * @param bool $isDeposit
     * 
     * @return void
     * @throws InsufficientFundsException
     */
    public function updateBalance(Balance $balance, float $amount, bool $isDeposit): void
    {
        if (!$isDeposit && $balance->amount < $amount) {
            throw new InsufficientFundsException("Insufficient funds");
        }

        $balance->amount = $isDeposit
            ? bcadd($balance->amount, $amount, 2)
            : bcsub($balance->amount, $amount, 2);

        $balance->save();
    }

    /**
     * Поиск и блокировка баланса по userId (баланс должен существовать)
     *
     * @param int $userId
     * @return Balance
     * @throws ModelNotFoundException
     */
    public function lockBalance(int $userId): Balance
    {
        $balance = Balance::where('user_id', $userId)
            ->lockForUpdate()
            ->first();
            
        if(!$balance){
           throw new ModelNotFoundException("Balance for user not found");
        };

        return $balance;
    }

    /**
     * Создание записи о балансе (если нет) и ее блокировка
     *
     * @param int $userId
     * @return Balance
     * @throws ModelNotFoundException
     */
    public function lockOrCreateBalance(int $userId): Balance
    {
        Balance::firstOrCreate(['user_id' => $userId]);

        return $this->lockBalance($userId);
    }
 
}