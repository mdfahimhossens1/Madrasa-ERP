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
    Schema::create('fee_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('institution_id');
            $table->unsignedBigInteger('fund_id');
            $table->unsignedBigInteger('ledger_id');
            $table->unsignedBigInteger('sub_ledger_id')->nullable();
            $table->unsignedBigInteger('fee_type_id')->nullable();
            $table->enum('type', ['ekkalin', 'monthly', 'others']);
            $table->string('name')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->bigInteger('number')->number()->unique();
            $table->timestamps();

            $table->index('institution_id');
            $table->index('fund_id');
            $table->index('ledger_id');
            $table->index('sub_ledger_id');
            $table->index('fee_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_groups');
    }
};
