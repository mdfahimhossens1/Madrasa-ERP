<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('institution_id');
            $table->string('teacher_id', 50);
            $table->string('designation', 100);
            $table->date('joining_date');
            $table->text('qualification')->nullable();
            $table->text('experience')->nullable();
            $table->text('expertise_subjects')->nullable();
            $table->string('salary_scale', 50)->nullable();
            $table->decimal('basic_salary', 10, 2)->nullable();
            $table->boolean('is_class_teacher')->default(false);
            $table->unsignedBigInteger('class_teacher_for')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->unique(['teacher_id', 'institution_id']);
            
            $table->index('institution_id');
            $table->index('teacher_id');
            $table->index('joining_date');
        });
    }

    public function upForeignKeys(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('institution_id')->references('id')->on('madrasas')->onDelete('cascade');
            $table->foreign('class_teacher_for')->references('id')->on('classes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};