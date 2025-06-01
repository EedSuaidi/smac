<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB; // <-- Import DB Facade
use App\Models\Currency; // <-- Import model Currency
use App\Models\WalletBalance; // <-- Import model WalletBalance
use Illuminate\Support\Facades\Log;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The "booted" method of the model.
     * This is executed once when the model is initialized.
     */
    protected static function booted()
    {
        parent::booted(); // Panggil parent booted method jika ada (biasanya tidak ada untuk Authenticatable)

        // Ketika user baru berhasil dibuat, buatkan wallet_balances USD default
        static::created(function ($user) {
            DB::transaction(function () use ($user) {
                // Cari ID mata uang USD dari tabel currencies
                $usdCurrency = Currency::where('symbol', 'USD')->first();

                if ($usdCurrency) {
                    // Buat entri WalletBalance baru untuk user dan USD dengan saldo 0
                    WalletBalance::create([
                        'user_id' => $user->id,
                        'currency_id' => $usdCurrency->id,
                        'balance' => '0.00000000' // Pastikan ini string untuk tipe decimal
                    ]);
                } else {
                    // Log error jika mata uang USD tidak ditemukan.
                    // Ini mengindikasikan bahwa tabel 'currencies' belum di-seed dengan USD.
                    Log::error('USD currency not found when creating user default wallet balance.', ['user_id' => $user->id]);
                    // Anda bisa memilih untuk melempar exception di sini jika ini adalah kondisi kritis
                    // throw new \Exception('Default USD currency not found in currencies table.');
                }
            });
        });
    }

    /**
     * Get the transactions for the user.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the wallet balances for the user.
     */
    public function walletBalances(): HasMany
    {
        return $this->hasMany(WalletBalance::class);
    }

    /**
     * Get the reports for the user.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
