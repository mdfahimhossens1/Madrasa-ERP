<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_settings', function (Blueprint $table) {
            $table->foreign('sub_ledger_id')
                  ->references('id')
                  ->on('sub_ledgers')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('fee_settings', function (Blueprint $table) {
            $table->dropForeign(['sub_ledger_id']);
        });
    }
};