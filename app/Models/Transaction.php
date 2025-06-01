<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'crypto_id',
        'user_id',
        'amount',
        'price',
        'total',
        'type',
        'transaction_date',
    ];

    protected static function booted()
    {
        static::created(function ($transaction) {
            $wallet = WalletBalance::firstOrCreate(
                [
                    'crypto_id' => $transaction->crypto_id,
                    'user_id' => $transaction->user_id,
                ],
                [
                    'balance' => 0, // Default balance jika belum ada
                ]
            );

            if ($transaction->type === 'buy') {
                $wallet->balance += $transaction->amount;
            } elseif ($transaction->type === 'sell') {
                $wallet->balance -= $transaction->amount;
            }

            $wallet->save();
        });
    }

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the crypto that the transaction belongs to.
     */
    public function crypto(): BelongsTo
    {
        return $this->belongsTo(Crypto::class);
    }
}
