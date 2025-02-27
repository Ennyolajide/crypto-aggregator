<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crypto_prices', function (Blueprint $table) {
            $table->id();
            $table->string('symbol');
            $table->decimal('price', 20, 8);
            $table->decimal('previous_price', 20, 8)->nullable();
            $table->decimal('last_btc', 20, 8);
            $table->decimal('lowest', 20, 8);
            $table->decimal('highest', 20, 8);
            $table->decimal('daily_change_percentage', 10, 2);
            $table->json('exchanges');
            $table->enum('trend', ['up', 'down', 'neutral'])->default('neutral');
            $table->timestamps();

            $table->index('symbol');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crypto_prices');
    }
};