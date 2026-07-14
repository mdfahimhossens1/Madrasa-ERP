<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('institution_id');
        $table->string('name');
        $table->decimal('fee', 10, 2)->default(0);
        $table->date('start_date');
        $table->date('end_date');
        $table->timestamps();

        $table->foreign('institution_id')->references('id')->on('madrasas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
