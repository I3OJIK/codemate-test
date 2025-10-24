<?php

namespace App\DTOs\Requests;

use App\DTOs\DTO;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;

class AccountTransactionDto extends DTO
{
    public function __construct(
        #[Exists('users', 'id')]
        public int $userId,

        #[Min(0)]
        public float $amount,
        
        #[Max(100)]
        public ?string $comment = null,
    ) {}
}