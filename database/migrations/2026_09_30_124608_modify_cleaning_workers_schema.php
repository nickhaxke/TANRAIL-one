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
        Schema::table('cleaning_workers', function (Blueprint $table) {
            // Rename phone to phone_number
            $table->renameColumn('phone', 'phone_number');

            // Drop gender and add id_number
            $table->dropColumn('gender');
            $table->string('id_number')->nullable()->after('last_name');

            // Drop global unique constraint on worker_id and add composite unique constraint
            $table->dropUnique('cleaning_workers_worker_id_unique');
            $table->unique(['business_unit_id', 'worker_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cleaning_workers', function (Blueprint $table) {
            $table->renameColumn('phone_number', 'phone');

            $table->dropColumn('id_number');
            $table->string('gender')->nullable()->after('phone');

            $table->dropUnique(['business_unit_id', 'worker_id']);
            $table->unique('worker_id', 'cleaning_workers_worker_id_unique');
        });
    }
};
