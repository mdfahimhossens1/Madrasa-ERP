<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_groups', function (Blueprint $table) {
            $table->foreign('madrasa_id')
                  ->references('id')
                  ->on('madrasas')
                  ->onDelete('cascade');
                  
            $table->foreign('fund_id')
                  ->references('id')
                  ->on('funds')
                  ->onDelete('cascade');
                  
            $table->foreign('ledger_id')
                  ->references('id')
                  ->on('ledgers')
                  ->onDelete('cascade');
                  
            $table->foreign('sub_ledger_id')
                  ->references('id')
                  ->on('sub_ledgers')
                  ->onDelete('set null');
                  
            $table->foreign('fee_type_id')
                  ->references('id')
                  ->on('fee_types')
                  ->onDelete('cascade');
        });

    }

    public function down(): void
    {
        Schema::table('fee_groups', function (Blueprint $table) {
            $table->dropForeign(['madrasa_id']);
            $table->dropForeign(['fund_id']);
            $table->dropForeign(['ledger_id']);
            $table->dropForeign(['sub_ledger_id']);
            $table->dropForeign(['fee_type_id']);
        });
    }
};