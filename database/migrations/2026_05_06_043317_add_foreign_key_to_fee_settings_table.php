<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_settings', function (Blueprint $table) {
          $table->foreignId('fee_group_id')
          ->nullable()
          ->after('class_id')
          ->constrained('fee_groups')
          ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('fee_settings', function (Blueprint $table) {
            $table->dropForeign(['fee_group_id']);
        });
    }
};