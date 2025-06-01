<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crypto_id')->constrained()->onDelete('cascade')->onUpdate('cascade');;
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->onUpdate('cascade');;
            $table->decimal('amount', 20, 8);
            $table->decimal('price', 20, 8);
            $table->decimal('total', 20, 8);
            $table->enum('type', ['buy', 'sell']);
            $table->date('transaction_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
