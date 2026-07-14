<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {

            $table->id();

            $table->string('module',100);
            // Student, Teacher, User, Fee, Reports

            $table->string('permission_name',150);
            // View Student

            $table->string('slug',150)->unique();
            // student.view

            $table->text('description')->nullable();

            $table->boolean('is_system')->default(true);

            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->index('module');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};