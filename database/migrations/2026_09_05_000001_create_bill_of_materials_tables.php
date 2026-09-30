<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bill_of_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('business_unit_id')->constrained('business_units')->onDelete('cascade');
            $table->foreignId('finished_item_id')->constrained('items')->onDelete('cascade');
            $table->string('name');
            $table->string('code');
            $table->decimal('yield_quantity', 15, 4)->default(1.0000);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['organization_id', 'code']);
        });

        Schema::create('bill_of_materials_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_of_materials_id')->constrained('bill_of_materials')->onDelete('cascade');
            $table->foreignId('ingredient_item_id')->constrained('items')->onDelete('cascade');
            $table->decimal('quantity', 15, 4);
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->decimal('scrap_percentage', 5, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bill_of_materials_items');
        Schema::dropIfExists('bill_of_materials');
    }
};
