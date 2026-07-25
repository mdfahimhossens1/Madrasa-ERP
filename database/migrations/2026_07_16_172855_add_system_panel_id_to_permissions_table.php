<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {

            $table->foreignId('system_panel_id')
                ->nullable()
                ->after('id')
                ->constrained('system_panels')
                ->cascadeOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {

            $table->dropForeign(['system_panel_id']);
            $table->dropColumn('system_panel_id');

        });
    }
};