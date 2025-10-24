<?php

namespace App\Services;

use App\DTOs\Models\TransactionDto;
use App\DTOs\Requests\AccountTransactionDto;
use App\DTOs\Requests\TransferDto;
use App\Enum\TransactionStatus as EnumTransactionStatus;
use App\Enums\TransactionStatus;
use App\Exceptions\InsufficientFundsException;
use App\Models\Transaction;
use App\Models\Balance;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TransactionService
{
    public function __construct(
        private BalanceService $balanceService,
    ) {}

    /**
     * Зачисление средств пользователю
     * 
     * @param AccountTransactionDto $data
     * 
     * @return Transaction
     * @throws ModelNotFoundException
     */
    public function deposit(AccountTransactionDto $data): Transaction
    {
       return DB::transaction(function () use ($data) {
            $balance = $this->balanceService->lockOrCreateBalance($data->userId);

            $transaction = Transaction::create([
                'user_id' => $data->userId,
                'status' => EnumTransactionStatus::DEPOSIT,
                'amount' => $data->amount,
                'comment' => $data->comment,
                'created_at' => now()
            ]);

            $this->balanceService->updateBalance($balance, $data->amount, true);

            return $transaction;
        });
    }

    /**
     * Снятие средств с баланса пользователя
     * 
     * @param AccountTransactionDto $data
     * 
     * @return Transaction
     * @throws ModelNotFoundException
     * @throws InsufficientFundsException
     */
    public function withdraw(AccountTransactionDto $data): Transaction
    {
       return DB::transaction(function () use ($data) {
            $balance = $this->balanceService->lockBalance($data->userId);

            $transaction = Transaction::create([
                'user_id' => $data->userId,
                'status' => EnumTransactionStatus::WITHDRAW,
                'amount' => $data->amount,
                'comment' => $data->comment,
                'created_at' => now()
            ]);

            $this->balanceService->updateBalance($balance, $data->amount, false);

            return $transaction;
        });
    }

    /**
     * Перевод средств от одного пользователля к другому
     * 
     * @param TransferDto $data
     * 
     * @return Transaction
     * @throws ModelNotFoundException
     * @throws InsufficientFundsException
     */
    public function transfer(TransferDto $data): Transaction
    {
       return DB::transaction(function () use ($data) {
            // блокировка баланса
            $fromBalance = $this->balanceService->lockBalance($data->fromUserId);
            $toBalance = $this->balanceService->lockOrCreateBalance($data->toUserId);

            $this->balanceService->updateBalance($fromBalance, $data->amount, false);
            $this->balanceService->updateBalance($toBalance, $data->amount, true);

            // Списание у отправителя
            $out = Transaction::create([
                'user_id' => $data->fromUserId,
                'related_user_id' => $data->toUserId,
                'status' => EnumTransactionStatus::TRANSFER_OUT,
                'amount' => $data->amount,
                'comment' => $data->comment,
                'created_at' => now()
            ]);

            // Зачисление у получателя
            Transaction::create([
                'user_id' => $data->toUserId,
                'related_user_id' => $data->fromUserId,
                'status' => EnumTransactionStatus::TRANSFER_IN,
                'amount' => $data->amount,
                'comment' => $data->comment,
                'created_at' => now()
            ]);
            return $out;
        });
    }

}