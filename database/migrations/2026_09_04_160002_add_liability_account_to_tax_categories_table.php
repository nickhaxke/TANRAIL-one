<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tax_categories', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('liability_account_id')->nullable()->after('description')->constrained('accounts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tax_categories', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
            $table->dropForeign(['liability_account_id']);
            $table->dropColumn('liability_account_id');
        });
    }
};
