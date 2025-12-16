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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2)->unsigned();
            $table->string('currency')->default('IDR');
            $table->date('expense_date');
            $table->string('payment_method');

            $table->json('ocr_data')->nullable();
            $table->decimal('subtotal', 15, 2)->nullable();
            $table->decimal('tax_amount', 15, 2)->nullable();
            $table->decimal('service_charge', 15, 2)->nullable();
            $table->decimal('tip_amount', 15, 2)->nullable();
            $table->decimal('discount_amount', 15, 2)->nullable();

            $table->boolean('is_shared')->default(false);
            $table->enum('split_type', ['none', 'equal', 'manual', 'items'])->default('none');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
