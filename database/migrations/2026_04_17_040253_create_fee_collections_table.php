<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ফি গ্রহণ - মূল transaction table
        Schema::create('fee_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained('madrasas')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->unsignedBigInteger('fee_setting_id')->nullable();
            $table->unsignedBigInteger('sub_ledger_id')->nullable();
            $table->string('receipt_no')->unique();
            $table->date('collection_date');
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('paid_amount', 10, 2)->default(0.00);
            $table->decimal('due_amount', 10, 2)->default(0.00);
            $table->string('month')->nullable();
            $table->enum('payment_method', ['cash', 'bank', 'mobile_banking'])->default('cash');
            $table->string('transaction_ref')->nullable(); // bKash/bank ref
            $table->enum('status', ['paid', 'partial', 'due'])->default('paid');
            $table->text('note')->nullable();
            $table->foreignId('collected_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_collections');
    }
};