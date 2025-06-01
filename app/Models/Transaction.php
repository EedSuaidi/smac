<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'currency_id',
        'user_id',
        'amount',
        'price',
        'total',
        'transaction_type',
        'transaction_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:8',
        'price' => 'decimal:8',
        'total' => 'decimal:8',
        'transaction_type' => 'string', // <-- Cast sebagai string (tidak menggunakan Enum)
        'transaction_date' => 'date',
    ];

    /**
     * The "booted" method of the model.
     * This is executed once when the model is initialized.
     */
    protected static function booted()
    {
        parent::booted(); // Panggil parent booted method jika ada

        // --- Event 'creating': Mengatur nilai default price dan total sebelum disimpan ---
        static::creating(function ($transaction) {
            // Eager load currency agar bisa diakses currency_type
            $transaction->load('currency'); // Memastikan relasi currency sudah ada atau di-load

            // Logika untuk mengisi price dan total berdasarkan tipe mata uang
            if ($transaction->currency->currency_type === 'fiat') {
                // Untuk mata uang fiat (misal USD, IDR)
                $transaction->price = '1.00'; // Harga per unit fiat selalu 1
                $transaction->total = (string) $transaction->amount; // Total sama dengan amount untuk fiat
            } else {
                // Untuk mata uang kripto (buy/sell)
                // Pastikan price tidak null. Jika dari form tidak diisi, set ke 0 sebagai fallback.
                // Sebaiknya, price harus selalu diisi oleh user atau dari API.
                if (empty($transaction->price)) {
                    Log::warning("Transaction price missing for crypto transaction ({$transaction->currency->symbol}). Defaulting to 0.00.");
                    $transaction->price = '0.00';
                }
                // Pastikan total terhitung jika price sudah diisi
                if (empty($transaction->total)) {
                    $transaction->total = (string)((float)$transaction->amount * (float)$transaction->price);
                }
            }

            // Pastikan semua nilai decimal yang diset secara langsung adalah string
            $transaction->price = (string) $transaction->price;
            $transaction->total = (string) $transaction->total;
        });


        // --- Event 'created': Mengupdate saldo wallet setelah transaksi sukses dibuat ---
        static::created(function ($transaction) {
            DB::transaction(function () use ($transaction) {
                // Dapatkan ID mata uang USD (asumsi sudah ada di tabel currencies)
                $usdCurrency = Currency::where('symbol', 'USD')->first();

                if (!$usdCurrency) {
                    // Log error jika USD currency tidak ditemukan
                    Log::error('USD currency not found when processing transaction balance update.', ['user_id' => $transaction->user_id, 'transaction_id' => $transaction->id]);
                    throw new \Exception("USD currency not found in database. Please seed it first.");
                }

                // 1. Update saldo mata uang utama yang ditransaksikan
                $mainWalletBalance = WalletBalance::firstOrCreate(
                    [
                        'user_id' => $transaction->user_id,
                        'currency_id' => $transaction->currency_id,
                    ],
                    [
                        'balance' => '0.00000000', // Default balance sebagai string untuk decimal
                    ]
                );

                if ($transaction->transaction_type === 'buy' || $transaction->transaction_type === 'deposit') {
                    $mainWalletBalance->increment('balance', $transaction->amount);
                } elseif ($transaction->transaction_type === 'sell' || $transaction->transaction_type === 'withdraw') {
                    // Validasi: pastikan saldo cukup sebelum mengurangi
                    if ($mainWalletBalance->balance < $transaction->amount) {
                        throw new \Exception("Insufficient balance for " . $transaction->currency->symbol . ".");
                    }
                    $mainWalletBalance->decrement('balance', $transaction->amount);
                }

                // 2. Update saldo USD untuk transaksi buy/sell kripto
                if ($transaction->currency->currency_type === 'crypto') {
                    $usdWalletBalance = WalletBalance::firstOrCreate(
                        [
                            'user_id' => $transaction->user_id,
                            'currency_id' => $usdCurrency->id,
                        ],
                        [
                            'balance' => '0.00', // Default balance sebagai string untuk decimal
                        ]
                    );

                    if ($transaction->transaction_type === 'buy') {
                        // Beli kripto mengurangi USD
                        if ($usdWalletBalance->balance < $transaction->total) {
                            throw new \Exception("Insufficient USD balance to buy " . $transaction->currency->symbol . ".");
                        }
                        $usdWalletBalance->decrement('balance', $transaction->total);
                    } elseif ($transaction->transaction_type === 'sell') {
                        // Jual kripto menambah USD
                        $usdWalletBalance->increment('balance', $transaction->total);
                    }
                }
            });
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
     * Get the currency that the transaction belongs to.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
