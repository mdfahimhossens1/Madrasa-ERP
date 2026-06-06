<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('academic_year_id');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->string('admission_no', 50)->unique();
            $table->date('admission_date');
            $table->string('admission_serial', 50)->nullable();
            $table->enum('financial_status', ['solvent', 'insolvent', 'orphan', 'helpless'])->default('solvent');
            $table->enum('residence_status', ['resident', 'non-resident', 'daycare', 'nightcare'])->default('non-resident');
            $table->enum('admission_type', ['new', 'old'])->default('new');
            $table->enum('status', ['active', 'transferred', 'graduated', 'dropped'])->default('active');
            $table->date('leaving_date')->nullable();
            $table->text('leaving_reason')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
          
            $table->index(['student_id', 'academic_year_id']);
            $table->index(['admission_no']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};