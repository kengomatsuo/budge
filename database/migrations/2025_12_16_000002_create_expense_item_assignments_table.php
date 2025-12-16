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
        Schema::create('expense_item_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('shared_expense_member_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity_assigned', 10, 2);
            $table->decimal('amount_assigned', 15, 2);
            $table->timestamps();

            $table->unique(['expense_item_id', 'shared_expense_member_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_item_assignments');
    }
};
