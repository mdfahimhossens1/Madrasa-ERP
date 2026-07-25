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
    $table->string('module',100)->nullable();
    $table->string('permission_name',150);

    $table->string('slug',150)->unique();

    $table->text('description')->nullable();

    $table->unsignedInteger('serial')->default(0);

    $table->boolean('is_system')->default(true);

    $table->boolean('is_active')->default(true);

    $table->timestamps();

});
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};