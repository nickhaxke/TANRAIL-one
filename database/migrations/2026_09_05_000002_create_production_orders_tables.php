<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('business_unit_id')->constrained('business_units')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('bill_of_materials_id')->constrained('bill_of_materials')->onDelete('cascade');
            $table->foreignId('finished_item_id')->constrained('items')->onDelete('cascade');
            $table->string('production_number');
            $table->decimal('target_quantity', 15, 4);
            $table->decimal('actual_quantity', 15, 4)->nullable();
            $table->string('status')->default('DRAFT');
            $table->foreignId('source_location_id')->constrained('inventory_locations')->onDelete('cascade');
            $table->foreignId('destination_location_id')->constrained('inventory_locations')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['organization_id', 'production_number']);
        });

        Schema::create('production_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained('production_orders')->onDelete('cascade');
            $table->foreignId('ingredient_item_id')->constrained('items')->onDelete('cascade');
            $table->decimal('required_quantity', 15, 4);
            $table->decimal('actual_quantity', 15, 4)->nullable();
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->decimal('scrap_percentage', 5, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_order_items');
        Schema::dropIfExists('production_orders');
    }
};
