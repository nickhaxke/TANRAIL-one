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
        Schema::table('supplier_invoices', function (Blueprint $table) {
            $table->text('discrepancy_reason')->nullable()->after('match_status');
            $table->json('match_details')->nullable()->after('discrepancy_reason');
            $table->foreignId('overridden_by')->nullable()->after('match_details')->constrained('users')->nullOnDelete();
            $table->timestamp('overridden_at')->nullable()->after('overridden_by');
            $table->text('override_reason')->nullable()->after('overridden_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {
            $table->dropForeign(['overridden_by']);
            $table->dropColumn([
                'discrepancy_reason',
                'match_details',
                'overridden_by',
                'overridden_at',
                'override_reason',
            ]);
        });
    }
};
