<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('madrasas', function (Blueprint $table) {
            $table->id();
            $table->string('madrasa_code', 50)->unique();
            $table->string('name', 255);
            $table->string('name_bn', 255)->nullable();
            $table->string('s_name', 255)->nullable();
            $table->string('email', 100)->nullable()->unique();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 100)->default('Bangladesh');
            $table->string('logo', 255)->nullable();
            $table->string('banner', 255)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('eiin_no', 50)->nullable();
            $table->year('established_year')->nullable();
            $table->string('registration_no', 100)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->date('subscription_end_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            $table->index('madrasa_code');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('madrasas');
    }
};