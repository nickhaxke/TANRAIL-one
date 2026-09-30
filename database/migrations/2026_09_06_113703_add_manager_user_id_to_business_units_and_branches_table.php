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
        Schema::table('business_units', function (Blueprint $table) {
            $table->foreignId('manager_user_id')->nullable()->after('cost_center')->constrained('users')->nullOnDelete();
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->foreignId('manager_user_id')->nullable()->after('email')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropConstrainedForeignId('manager_user_id');
        });

        Schema::table('business_units', function (Blueprint $table) {
            $table->dropConstrainedForeignId('manager_user_id');
        });
    }
};
