<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_collection_details', function (Blueprint $table) {
            $table->foreign('fee_collection_id')
                  ->references('id')
                  ->on('fee_collections')
                  ->onDelete('cascade');
                  
            $table->foreign('fee_id')
                  ->references('id')
                  ->on('fee_groups')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('fee_collection_details', function (Blueprint $table) {
            $table->dropForeign(['fee_collection_id']);
            $table->dropForeign(['fee_id']);
        });
    }
};