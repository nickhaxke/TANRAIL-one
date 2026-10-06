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
        Schema::create('work_activity_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_activity_id')->constrained('work_activities')->cascadeOnDelete();
            $table->foreignId('cleaning_service_template_item_id')->nullable()->constrained('cleaning_service_template_items', 'id', 'wai_template_item_id_fk')->nullOnDelete();
            $table->string('label');
            $table->string('status')->default('Pending');
            $table->decimal('target_qty', 10, 2)->nullable();
            $table->decimal('done_qty', 10, 2)->nullable();
            $table->string('unit')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('remarks')->nullable();
            $table->json('attributes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_activity_items');
    }
};
