<?php

namespace App\DTOs\Requests;

use App\DTOs\DTO;
use Spatie\LaravelData\Attributes\Validation\Different;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\GreaterThan;
use Spatie\LaravelData\Attributes\Validation\Max;

class TransferDto extends DTO
{
    public function __construct(
        #[Exists('users', 'id')]
        public int $fromUserId,

        #[Exists('users', 'id'), Different('fromUserId')]
        public int $toUserId,

        #[GreaterThan(0)]
        public float $amount,

        #[Max(100)]
        public ?string $comment = null,
    ) {}

    public static function messages(): array
    {
        return [
            'from_user_id.exists' => 'The selected sender user ID  not found',
            'to_user_id.exists' => 'The selected recipient user ID not found',
            'to_user_id.different' => 'The recipient user ID must be different from the sender user ID',
        ];
    }
}