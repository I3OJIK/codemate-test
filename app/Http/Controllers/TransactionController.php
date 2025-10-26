<?php

namespace App\Http\Controllers;

use App\DTOs\Models\TransactionDto;
use App\DTOs\Requests\InternalTransactionDto;
use App\DTOs\Requests\TransferDto;
use App\Services\TransactionService;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService,
    ) {}
    
    /**
     * Пополнение счета пользователя
     * 
     * @param InternalTransactionDto $data
     * 
     * @return TransactionDto
     */
    public function deposit(InternalTransactionDto $data): TransactionDto
    {
        $transaction = $this->transactionService->deposit($data);

        return TransactionDto::from($transaction);
    }

    /**
     * Снятие средств пользователя
     * 
     * @param InternalTransactionDto $data
     * 
     * @return TransactionDto
     */
    public function withdraw(InternalTransactionDto $data): TransactionDto
    {
        $transaction = $this->transactionService->withdraw($data);

        return TransactionDto::from($transaction);
    }

    /**
     * Перевод средств 
     * 
     * @param TransferDto $data
     * 
     * @return TransactionDto
     */
    public function transfer(TransferDto $data): TransactionDto
    {
        $transaction = $this->transactionService->transfer($data);

        return TransactionDto::from($transaction);
    }
}
