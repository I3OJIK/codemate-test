<?php

namespace App\DTOs\Models;

use App\DTOs\DTO;
use App\Enum\TransactionStatus;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;

class TransactionDto extends DTO
{
    public function __construct(
        public int $id,

        public int $userId,

        public ?int $relatedUserId,

        public TransactionStatus $status,

        public float $amount,

        public ?string $comment = null,

        public string $createdAt,
        public string $updatedAt,
    ) {}
}