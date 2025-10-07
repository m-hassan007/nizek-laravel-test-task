<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_prices', function (Blueprint $table) {
            $table->id();
            $table->string('company_symbol', 10)->index();
            $table->string('company_name')->nullable();
            $table->date('date');
            $table->decimal('open_price', 15, 4)->nullable();
            $table->decimal('high_price', 15, 4)->nullable();
            $table->decimal('low_price', 15, 4)->nullable();
            $table->decimal('close_price', 15, 4);
            $table->decimal('adjusted_close', 15, 4)->nullable();
            $table->bigInteger('volume')->nullable();
            $table->timestamps();

            // Composite index for efficient queries
            $table->index(['company_symbol', 'date']);
            $table->index(['date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_prices');
    }
};
