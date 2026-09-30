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
        Schema::create('processed_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_class', 90);
            $table->string('reference_id', 90);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['event_class', 'reference_id'], 'processed_events_class_ref_unique');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processed_events');
    }
};
