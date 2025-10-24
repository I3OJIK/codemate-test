<?php

namespace App\Http\Controllers;

use App\DTOs\Models\TransactionDto;
use App\DTOs\Requests\AccountTransactionDto;
use App\DTOs\Requests\TransferDto;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService,
    ) {}
    
    /**
     * Пополнение счета пользователя
     * 
     * @param AccountTransactionDto $data
     * 
     * @return TransactionDto
     */
    public function deposit(AccountTransactionDto $data): TransactionDto
    {
        $transaction = $this->transactionService->deposit($data);

        return TransactionDto::from($transaction);
    }

    public function withdraw(AccountTransactionDto $data): TransactionDto
    {
        $transaction = $this->transactionService->withdraw($data);

        return TransactionDto::from($transaction);
    }

    public function transfer(TransferDto $data): TransactionDto
    {
        $transaction = $this->transactionService->transfer($data);

        return TransactionDto::from($transaction);
    }
}
