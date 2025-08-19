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
        Schema::create('borrow_transaction', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrow_requests_id')->constrained('borrow_requests')->onDelete('cascade');
            $table->dateTime('checked_out_at');
            $table->dateTime('checked_in_at');
            $table->integer('penalty_amount')->default(0);
            $table->boolean('notes')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrow_transaction');
    }
};
