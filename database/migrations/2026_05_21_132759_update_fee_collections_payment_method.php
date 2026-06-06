<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_collections', function (Blueprint $table) {

            $table->dropColumn('payment_method');
            $table->unsignedBigInteger('payment_method_id')->nullable()->after('due_amount');
            $table->foreign('payment_method_id')
                ->references('id')
                ->on('payment_methods')
                ->onDelete('set null');
            
            // এখানে pay_type কলাম যোগ করা হচ্ছে
            if (!Schema::hasColumn('fee_collections', 'pay_type')) {
                $table->enum('pay_type', ['monthly', 'admission'])
                      ->default('monthly')
                      ->after('month'); // month কলামের পরে বসানো হচ্ছে
            }
        });
    }

    public function down(): void
    {
        Schema::table('fee_collections', function (Blueprint $table) {

            $table->dropForeign(['payment_method_id']);
            $table->dropColumn('payment_method_id');
            
            // pay_type কলাম ড্রপ করা হচ্ছে
            if (Schema::hasColumn('fee_collections', 'pay_type')) {
                $table->dropColumn('pay_type');
            }

            $table->enum('payment_method', [
                'cash',
                'bank',
                'mobile_banking'
            ])->default('cash');
        });
    }
};