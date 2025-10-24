<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    /**
     * Баланс пользователя
     */
    public function balance(): BelongsTo
    {
        return $this->belongsTo(Balance::class);
    }
}
