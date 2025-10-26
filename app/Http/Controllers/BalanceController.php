<?php

namespace App\Http\Controllers;

use App\DTOs\Models\BalanceDto;
use App\DTOs\Requests\ShowBalanceDto;
use App\Services\BalanceService;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function __construct(
        private BalanceService $balanceService,
    ) {}

    /**
     * Баланс пользователя
     * 
     * @param int $userId
     * 
     * @return BalanceDto
     */
    public function show(int $userId): BalanceDto
    {
        $balance = $this->balanceService->getBalance($userId);

        return new BalanceDto($userId, $balance);
    }
}
