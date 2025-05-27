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
        Schema::table('receipt_item', function (Blueprint $table) {
            // Add new columns
            // $table->string('condition_out')->nullable()->after('status');
            // $table->string('condition_in')->nullable()->after('status');
            // $table->text('notes')->nullable()->after('date_returned'); // date_returned is an existing column

            // Modify existing status enum
            // Same caveats as with the receipt table for ENUM modification.
            // The default could be 'checked_out' for new checkout items.
            $table->enum('status', [
                        'checked_out', // New (replaces 'in use')
                        'returned',    // Existing
                        'lost',        // Existing
                        'damaged'      // New
                   ])->default('checked_out')->change(); // Or remove default if set manually
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipt_item', function (Blueprint $table) {
            $table->dropColumn([
                'condition_out',
                'condition_in',
                'notes'
            ]);

            // Revert status enum to its original state
            $table->enum('status', [
                        'in use',
                        'returned',
                        'lost'
                   ])->default(null)->change(); // Set appropriate old default or remove
        });
    }
};