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
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('trading_name')->nullable()->after('name');
            $table->string('tin_number')->nullable()->after('trading_name');
            $table->string('registration_number')->nullable()->after('tin_number');
            $table->string('industry')->nullable()->after('registration_number');
            $table->string('country')->nullable()->after('industry');
            $table->string('address')->nullable()->after('country');
            $table->string('postal_code')->nullable()->after('address');
            $table->string('city')->nullable()->after('postal_code');
            $table->string('phone')->nullable()->after('city');
            $table->string('email')->nullable()->after('phone');
            $table->string('website')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn([
                'trading_name',
                'tin_number',
                'registration_number',
                'industry',
                'country',
                'address',
                'postal_code',
                'city',
                'phone',
                'email',
                'website',
            ]);
        });
    }
};
