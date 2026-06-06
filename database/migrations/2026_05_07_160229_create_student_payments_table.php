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
        Schema::create('student_payments', function (Blueprint $table) {
    $table->id();

    $table->unsignedBigInteger('madrasa_id');
    $table->unsignedBigInteger('student_id');
    $table->unsignedBigInteger('user_id'); // shortcut for faster query

    $table->unsignedBigInteger('fee_id')->nullable(); // optional (future use)

    $table->string('month')->nullable(); // Jan, Feb
    $table->string('pay_type'); // admission / monthly

    $table->decimal('amount', 10, 2);
    $table->decimal('discount', 10, 2)->default(0);

    $table->string('method'); // Cash, Mobile, Bank

    $table->unsignedBigInteger('cashier_id')->nullable();

    $table->date('payment_date');
    $table->string('voucher_no');

    $table->text('note')->nullable();

    $table->timestamps();

    // index
    $table->index('student_id');
    $table->index('user_id');
    $table->index('madrasa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_payments');
    }
};
