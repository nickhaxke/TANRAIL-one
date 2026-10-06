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
        Schema::create('cleaning_service_template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cleaning_service_template_id')->constrained('cleaning_service_templates', 'id', 'csti_template_id_fk')->cascadeOnDelete();
            $table->string('label');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->decimal('default_target_qty', 10, 2)->nullable();
            $table->string('unit')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cleaning_service_template_items');
    }
};
