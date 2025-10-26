<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Balance extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Пользователь, которому принадлежит баланас
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
