<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('booking_number');
            $table->string('event_name');
            $table->string('event_type')->nullable();
            $table->string('venue_location')->nullable();
            $table->dateTime('start_date_time');
            $table->dateTime('end_date_time');
            $table->integer('guest_count');
            $table->decimal('subtotal', 15, 4)->default(0);
            $table->decimal('tax_amount', 15, 4)->default(0);
            $table->decimal('total_amount', 15, 4)->default(0);
            $table->decimal('deposit_required_amount', 15, 4)->default(0);
            $table->decimal('deposit_paid_amount', 15, 4)->default(0);
            $table->string('status')->default('DRAFT');
            $table->foreignId('deposit_invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->foreignId('final_invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['business_unit_id', 'booking_number']);
            $table->index(['organization_id', 'business_unit_id']);
            $table->index('customer_id');
            $table->index(['start_date_time', 'end_date_time']);
            $table->index('status');
        });

        Schema::create('event_booking_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_booking_id')->constrained('event_bookings')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 15, 4);
            $table->decimal('unit_price', 15, 4);
            $table->decimal('subtotal', 15, 4);
            $table->foreignId('tax_category_id')->nullable()->constrained('tax_categories')->nullOnDelete();
            $table->decimal('tax_amount', 15, 4)->default(0);
            $table->decimal('total', 15, 4);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('event_booking_id');
        });

        Schema::create('event_booking_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_booking_id')->constrained('event_bookings')->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('vip_status')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('event_booking_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_booking_attendees');
        Schema::dropIfExists('event_booking_packages');
        Schema::dropIfExists('event_bookings');
    }
};
