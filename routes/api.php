<?php

use App\Http\Controllers\BalanceController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;


Route::post('/deposit', [TransactionController::class, 'deposit']);
Route::post('/withdraw', [TransactionController::class, 'withdraw']);
Route::post('/transfer', [TransactionController::class, 'transfer']);
Route::get('/balance/{user_id}', [BalanceController::class, 'show']);


