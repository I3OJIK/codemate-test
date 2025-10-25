<?php

namespace App\Services;

use App\Exceptions\InsufficientFundsException;
use App\Models\Balance;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

        if ($isDeposit) {
            $balance->increment('amount', $amount);
        } else {
            $balance->decrement('amount', $amount);
        }
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
           throw new ModelNotFoundException("Balance for the specified user not found");
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
    public function getOrCreateLockedBalance(int $userId): Balance
    {
        Balance::firstOrCreate(['user_id' => $userId]);

        return $this->lockBalance($userId);
    }
 
}