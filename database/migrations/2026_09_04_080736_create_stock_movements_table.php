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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->string('type');
            $table->foreignId('source_location_id')->nullable()->constrained('inventory_locations')->restrictOnDelete();
            $table->foreignId('destination_location_id')->nullable()->constrained('inventory_locations')->restrictOnDelete();
            $table->decimal('quantity', 14, 4);
            $table->nullableMorphs('reference');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
