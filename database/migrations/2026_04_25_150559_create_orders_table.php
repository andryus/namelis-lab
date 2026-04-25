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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // NML-2025-00001
            $table->foreignId('clinic_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_tech_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('patient_ref'); // internal reference only, no full name
            $table->enum('impression_type', ['digital', 'physical'])->default('physical');
            $table->string('stl_url')->nullable();
            $table->string('stl_external_url')->nullable();
            $table->enum('status', [
                'draft',
                'awaiting_pickup',
                'in_transit',
                'at_lab',
                'in_production',
                'quality_check',
                'awaiting_delivery',
                'delivered',
                'cancelled'
            ])->default('draft');
            $table->date('requested_delivery_date')->nullable();
            $table->text('clinical_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->decimal('estimated_price', 8, 2)->nullable();
            $table->string('barcode')->unique()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
