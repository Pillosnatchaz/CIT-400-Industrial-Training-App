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
        Schema::create('receipt_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_id')->constrained('receipt')->onDelete('cascade');
            $table->foreignId('item_stock_id')->constrained('item_stocks')->onDelete('cascade');
            $table->enum('status', ['in use', 'returned', 'lost']);
            $table->date('date_returned')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_item');
    }
};
