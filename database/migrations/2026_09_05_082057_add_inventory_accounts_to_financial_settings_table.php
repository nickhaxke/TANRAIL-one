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
        Schema::table('financial_settings', function (Blueprint $table) {
            $table->foreignId('default_raw_materials_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('default_finished_goods_account_id')->nullable()->constrained('accounts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_settings', function (Blueprint $table) {
            $table->dropForeign(['default_raw_materials_account_id']);
            $table->dropColumn('default_raw_materials_account_id');
            $table->dropForeign(['default_finished_goods_account_id']);
            $table->dropColumn('default_finished_goods_account_id');
        });
    }
};
