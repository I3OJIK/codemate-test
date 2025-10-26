<?php

namespace App\DTOs\Requests;

use App\DTOs\DTO;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\GreaterThan;
use Spatie\LaravelData\Attributes\Validation\Max;

class InternalTransactionDto extends DTO
{
    public function __construct(
        #[Exists('users', 'id')]
        public int $userId,

        #[GreaterThan(0)]
        public float $amount,

        #[Max(100)]
        public ?string $comment = null,
    ) {}

    public static function messages(): array
    {
        return [
            'user_id.exists' => 'The selected user ID  not found',
        ];
    }

}