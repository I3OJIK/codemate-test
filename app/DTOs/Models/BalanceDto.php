<?php

namespace App\DTOs\Models;

use App\DTOs\DTO;

class BalanceDto extends DTO
{
    public function __construct(

        public int $userId,

        public float $amount,
    ) {}
}