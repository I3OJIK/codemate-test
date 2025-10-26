<?php

namespace App\Services;

use App\DTOs\Requests\InternalTransactionDto;
use App\DTOs\Requests\TransferDto;
use App\Enum\TransactionStatus;
use App\Exceptions\InsufficientFundsException;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    private int $retries;

    public function __construct(
        private BalanceService $balanceService,
    ) {
        $this->retries = config('database.transactions.retries');
    }


    /**
     * Зачисление средств пользователю
     * 
     * @param InternalTransactionDto $data
     * 
     * @return Transaction
     * @throws ModelNotFoundException
     */
    public function deposit(InternalTransactionDto $data): Transaction
    {
       return DB::transaction(function () use ($data) {
            $balance = $this->balanceService->getOrCreateLockedBalance($data->userId);

            $transaction = Transaction::create([
                'user_id' => $data->userId,
                'status' => TransactionStatus::DEPOSIT,
                'amount' => $data->amount,
                'comment' => $data->comment,
            ]);

            $this->balanceService->updateBalance($balance, $data->amount, true);

            return $transaction;
        }, $this->retries);
    }

    /**
     * Снятие средств с баланса пользователя
     * 
     * @param InternalTransactionDto $data
     * 
     * @return Transaction
     * @throws ModelNotFoundException
     * @throws InsufficientFundsException
     */
    public function withdraw(InternalTransactionDto $data): Transaction
    {
       return DB::transaction(function () use ($data) {
            $balance = $this->balanceService->lockBalance($data->userId);

            $transaction = Transaction::create([
                'user_id' => $data->userId,
                'status' => TransactionStatus::WITHDRAW,
                'amount' => $data->amount,
                'comment' => $data->comment,
            ]);

            $this->balanceService->updateBalance($balance, $data->amount, false);

            return $transaction;
        }, $this->retries);
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
            // сортировка user_id(для уменшения шансов возникновение дедлоков)
            $balances = [$data->fromUserId, $data->toUserId];
            sort($balances);

            foreach ($balances as $userId) {
                $lockedBalances[$userId] = $this->balanceService->lockBalance($userId);
            }
            $fromBalance = $lockedBalances[$data->fromUserId];
            $toBalance = $lockedBalances[$data->toUserId];

            //обновление баланса у отправителя и получателя
            $this->balanceService->updateBalance($fromBalance, $data->amount, false);
            $this->balanceService->updateBalance($toBalance, $data->amount, true);

            // Транзакция списания у отправителя
            $out = Transaction::create([
                'user_id' => $data->fromUserId,
                'related_user_id' => $data->toUserId,
                'status' => TransactionStatus::TRANSFER_OUT,
                'amount' => $data->amount,
                'comment' => $data->comment,
            ]);

            // Транзакция зачисления у получателя
            Transaction::create([
                'user_id' => $data->toUserId,
                'related_user_id' => $data->fromUserId,
                'status' => TransactionStatus::TRANSFER_IN,
                'amount' => $data->amount,
                'comment' => $data->comment,
            ]);
            return $out;
        }, $this->retries);
    }



}