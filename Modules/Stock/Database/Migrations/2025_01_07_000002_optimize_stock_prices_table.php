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
        Schema::table('stock_prices', function (Blueprint $table) {
            // Add additional indexes for better query performance
            $table->index(['company_symbol', 'date', 'close_price'], 'idx_symbol_date_close');
            $table->index(['date', 'company_symbol'], 'idx_date_symbol');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_prices', function (Blueprint $table) {
            $table->dropIndex('idx_symbol_date_close');
            $table->dropIndex('idx_date_symbol');
        });
    }
};
