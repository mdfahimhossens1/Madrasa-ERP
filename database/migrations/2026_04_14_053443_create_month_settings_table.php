<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('month_settings', function (Blueprint $table) {
            $table->id();
            $table->string('academic_year', 10);
            $table->unsignedBigInteger('class_id');
            $table->string('month_1')->nullable();
            $table->string('month_2')->nullable();
            $table->string('month_3')->nullable();
            $table->string('month_4')->nullable();
            $table->string('month_5')->nullable();
            $table->string('month_6')->nullable();
            $table->string('month_7')->nullable();
            $table->string('month_8')->nullable();
            $table->string('month_9')->nullable();
            $table->string('month_10')->nullable();
            $table->string('month_11')->nullable();
            $table->string('month_12')->nullable();
            $table->timestamps();

            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->unique(['academic_year', 'class_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('month_settings');
    }
};