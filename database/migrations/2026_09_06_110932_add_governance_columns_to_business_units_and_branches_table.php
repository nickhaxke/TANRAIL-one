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
            $table->text('description')->nullable()->after('name');
            $table->string('category')->nullable()->after('description');
            $table->string('cost_center')->nullable()->after('category');
            $table->string('manager_name')->nullable()->after('cost_center');
            $table->string('manager_email')->nullable()->after('manager_name');
            $table->string('manager_phone')->nullable()->after('manager_email');
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->string('facility_type')->nullable()->after('name');
            $table->string('city')->nullable()->after('address');
            $table->string('phone')->nullable()->after('city');
            $table->string('email')->nullable()->after('phone');
            $table->string('manager_name')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn([
                'facility_type',
                'city',
                'phone',
                'email',
                'manager_name',
            ]);
        });

        Schema::table('business_units', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'category',
                'cost_center',
                'manager_name',
                'manager_email',
                'manager_phone',
            ]);
        });
    }
};
