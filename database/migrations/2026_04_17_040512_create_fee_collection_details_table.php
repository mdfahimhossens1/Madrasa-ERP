<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_collection_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fee_collection_id');
            $table->unsignedBigInteger('fee_id');
            $table->string('month_year')->nullable(); 
            $table->decimal('amount', 10, 2);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('paid_amount', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_collection_details');
    }
};