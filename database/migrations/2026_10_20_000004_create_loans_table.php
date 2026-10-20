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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignId('borrower_id')->constrained('users')->restrictOnDelete();
            $table->dateTime('borrowed_at')->index();
            $table->dateTime('due_at')->nullable()->index();
            $table->dateTime('returned_at')->nullable()->index();
            $table->string('checkout_condition');
            $table->string('return_condition')->nullable();
            $table->enum('status', ['active', 'returned', 'overdue'])->default('active')->index();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
