<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_settings', function (Blueprint $table) {
            
            $table->id();

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->cascadeOnDelete();

            $table->foreignId('class_id')
                ->nullable()
                ->constrained('classes')
                ->nullOnDelete();

            $table->unsignedBigInteger('sub_ledger_id')->nullable();
            $table->unsignedBigInteger('madrasa_id')->nullable()->index();
            $table->decimal('chattra_abashik_new', 15, 2)->nullable();
            $table->decimal('chattra_abashik_old', 15, 2)->nullable();
            $table->decimal('chattra_onabashik_new', 15, 2)->nullable();
            $table->decimal('chattra_onabashik_old', 15, 2)->nullable();
            $table->decimal('chattra_dekeyr_new', 15, 2)->nullable();
            $table->decimal('chattra_dekeyr_old', 15, 2)->nullable();
            $table->decimal('chattra_nightcare_new', 15, 2)->nullable();
            $table->decimal('chattra_nightcare_old', 15, 2)->nullable();
            $table->boolean('chattra_checked')->default(false);

            $table->decimal('chhatri_abashik_new', 15, 2)->nullable();
            $table->decimal('chhatri_abashik_old', 15, 2)->nullable();
            $table->decimal('chhatri_onabashik_new', 15, 2)->nullable();
            $table->decimal('chhatri_onabashik_old', 15, 2)->nullable();
            $table->decimal('chhatri_dekeyr_new', 15, 2)->nullable();
            $table->decimal('chhatri_dekeyr_old', 15, 2)->nullable();
            $table->decimal('chhatri_nightcare_new', 15, 2)->nullable();
            $table->decimal('chhatri_nightcare_old', 15, 2)->nullable();

            $table->boolean('chhatri_checked')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_settings');
    }
};