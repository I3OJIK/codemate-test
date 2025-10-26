<?php

namespace App\DTOs\Models;

use App\DTOs\DTO;
use App\Enum\TransactionStatus;

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