<?php

namespace App\DTOs\Models;

use App\DTOs\DTO;
use App\Enum\TransactionStatus;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;

class BalanceDto extends DTO
{
    public function __construct(

        public int $userId,

        public float $amount,
    ) {}
}