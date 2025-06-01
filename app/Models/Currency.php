<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'symbol',
        'currency_type',
    ];

    /**
     * The attributes that should be cast.
     * Tidak menggunakan Enum, jadi cast ke string saja jika diperlukan, atau tidak perlu di-cast.
     * Laravel akan menangani tipe ENUM di DB sebagai string secara otomatis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'currency_type' => 'string', // Opsional, Laravel sudah menganggapnya string
    ];

    /**
     * Get the transactions for the currency.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the wallet balances for the currency.
     */
    public function walletBalances(): HasMany
    {
        return $this->hasMany(WalletBalance::class);
    }
}
