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
        Schema::table('cleaning_service_types', function (Blueprint $table) {
            $table->string('code')->unique()->after('business_unit_id')->nullable();
        });

        Schema::table('work_activities', function (Blueprint $table) {
            $table->foreignId('cleaning_service_type_id')->nullable()->constrained('cleaning_service_types', 'id', 'wa_service_type_id_fk')->nullOnDelete();
            $table->foreignId('cleaning_service_template_id')->nullable()->constrained('cleaning_service_templates', 'id', 'wa_service_template_id_fk')->nullOnDelete();
            $table->string('reference')->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('work_activities', function (Blueprint $table) {
            $table->dropForeign('wa_service_type_id_fk');
            $table->dropForeign('wa_service_template_id_fk');
            $table->dropForeign(['created_by_id']);
            $table->dropColumn(['cleaning_service_type_id', 'cleaning_service_template_id', 'reference', 'created_by_id']);
        });

        Schema::table('cleaning_service_types', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
