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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_tag')->unique();
            $table->string('name')->index();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('brand');
            $table->string('model');
            $table->string('serial_number')->nullable()->index();
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('condition', ['good', 'minor_damage', 'major_damage'])->default('good')->index();
            $table->enum('status', ['available', 'assigned', 'borrowed', 'maintenance', 'retired'])->default('available')->index();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
