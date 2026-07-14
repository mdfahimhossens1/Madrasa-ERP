<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('institution_id');
            $table->unsignedBigInteger('academic_year_id');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('guardian_user_id')->nullable();
            $table->string('student_id', 50);
            $table->boolean('is_hostel')->default(false);
            $table->boolean('is_transport')->default(false);
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'institution_id']);
            $table->index('user_id');
            $table->index('institution_id');
            $table->index('academic_year_id');
            $table->index('class_id');
            $table->index('section_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};