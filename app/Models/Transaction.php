<?php

namespace App\Models;

use App\Enum\TransactionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'related_user_id',
        'status',
        'amount',
        'comment',
        'created_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'status' => TransactionStatus::class,
    ];

    /**
     * Пользователь, которому принадлежит транзакция
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Пользователь, связанный с транзакцией
     */
    public function relatedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'related_user_id');
    }

}
