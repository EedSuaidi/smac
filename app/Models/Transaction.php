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
        'price_at_transaction',
        'fiat_amount',
        'type',
    ];

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
