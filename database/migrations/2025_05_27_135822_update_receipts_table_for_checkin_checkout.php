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
        Schema::table('receipt', function (Blueprint $table) {
            // Add new columns
            $table->string('receipt_number')->unique()->after('id'); // Or make it non-nullable if generated immediately
            $table->enum('type', ['checkout', 'checkin'])->default('checkout')->after('project_id');
            $table->date('actual_return_date')->nullable()->after('expected_return_date');
            $table->text('notes')->nullable()->after('status'); // status is an existing column
            
            // Self-referencing foreign key for check-in receipts linking to original checkout
            $table->foreignId('parent_checkout_receipt_id')
                  ->nullable()
                  ->after('user_id') // Adjust position as preferred
                  ->constrained('receipt') // Assuming 'receipt' is the correct table name
                  ->onDelete('set null'); // Or 'restrict', 'cascade' depending on desired behavior

            // Optional: If the user creating the receipt is different from the one borrowing
            $table->foreignId('borrower_user_id')
                  ->nullable()
                  ->after('user_id') // Adjust position as preferred
                  ->constrained('users')
                  ->onDelete('set null');

            // Modify existing status enum
            // IMPORTANT: Modifying ENUMs can be tricky. This attempts to change it.
            // For some databases (like older MySQL or SQLite), this might require workarounds
            // or dropping and re-adding the column if direct modification isn't supported smoothly.
            // The new default will be 'draft' for checkouts.
            $table->enum('status', [
                        'draft', // New
                        'checked_out', // New (renamed from 'approved' or new concept)
                        'partially_returned', // New
                        'completed', // Existing
                        'overdue', // New
                        // Keep old values if necessary for a transition period or map them
                        'pending', // Existing (could map to 'draft')
                        'approved' // Existing (could map to 'checked_out')
                   ])->default('draft')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipt', function (Blueprint $table) {
            // Drop foreign keys first if they were added by this migration
            // Note: Dropping foreign keys requires specifying the index name or an array of columns.
            // Laravel generates index names like: tablename_columnname_foreign
            $table->dropForeign(['parent_checkout_receipt_id']);
            $table->dropForeign(['borrower_user_id']);

            $table->dropColumn([
                'receipt_number',
                'type',
                'actual_return_date',
                'notes',
                'parent_checkout_receipt_id',
                'borrower_user_id'
            ]);

            // Revert status enum to its original state
            // This also has the same caveats as modifying it in the up() method.
            $table->enum('status', [
                        'pending',
                        'approved',
                        'completed'
                   ])->default('pending')->change();
        });
    }
};