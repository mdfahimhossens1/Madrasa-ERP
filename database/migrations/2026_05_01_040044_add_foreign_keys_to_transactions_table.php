<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        
        Schema::table('transactions', function (Blueprint $table) {
            // আগে foreign keys না থাকলে যোগ করুন
            if (!Schema::hasTable('funds')) return;
            
            $table->foreign('fund_id')
                  ->references('id')
                  ->on('funds')
                  ->onDelete('set null');
                  
            $table->foreign('payment_method_id')
                  ->references('id')
                  ->on('payment_methods')
                  ->onDelete('set null');
                  
            $table->foreign('cashier_id')
                  ->references('id')
                  ->on('cashiers')
                  ->onDelete('set null');
        });
        
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['fund_id']);
            $table->dropForeign(['payment_method_id']);
            $table->dropForeign(['cashier_id']);
        });
        
        Schema::enableForeignKeyConstraints();
    }
};