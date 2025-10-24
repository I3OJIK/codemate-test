<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::post('/deposit', [TransactionController::class, 'deposit']);
Route::post('/withdraw', [BalanceController::class, 'withdraw']);
Route::post('/transfer', [BalanceController::class, 'transfer']);
Route::get('/balance/{user_id}', [BalanceController::class, 'getBalance']);


