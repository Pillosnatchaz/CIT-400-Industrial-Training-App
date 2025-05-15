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
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users');
            $table->string('entity_type');
            // $table->enum('entity_type', ['users', 'warehouse', 'items', 'projects', 'receipt']);
            $table->unsignedBigInteger('entity_id');
            $table->enum('action', ['created', 'updated', 'deleted']);
            $table->text('notes')->nullable();
            $table->timestamp('performed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};
