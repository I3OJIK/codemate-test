<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class ApiExceptionHandler extends Handler
{
    /**
     * Обработка исключения и возврат JSON
     */
    public function render($request, Throwable $e): JsonResponse
    {
        // Логируем исключение
        Log::error($e->getMessage(), [
            'exception' => $e,
            'url' => $request->url(),
        ]);

        // недостаточно средств
        if ($e instanceof InsufficientFundsException) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 409);
        }

         // Модель не найдена
         if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }

        // Валидация
        if ($e instanceof ValidationException) {
            return response()->json([
                'message'   => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        }

        // Для всех остальных исключений вызываем стандартный render
        return parent::render($request, $e);
    }
}
