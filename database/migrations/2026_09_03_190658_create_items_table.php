<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_unit_id')->constrained('business_units')->onDelete('cascade');
            $table->string('sku');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type');
            $table->boolean('track_inventory')->default(true);
            $table->decimal('base_price', 10, 2)->default(0);
            $table->foreignId('tax_category_id')->nullable()->constrained('tax_categories')->nullOnDelete();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->boolean('status')->default(true);
            $table->timestamps();

            // SKU must be unique within a Business Unit
            $table->unique(['business_unit_id', 'sku']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
