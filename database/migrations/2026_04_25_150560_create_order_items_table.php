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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->integer('tooth_fdi'); // FDI tooth number: 11-48
            $table->foreignId('work_category_id')->constrained();
            $table->string('vita_shade')->nullable(); // VITA Classic / 3D-Master
            $table->enum('mounting', ['single', 'bridge_pillar', 'bridge_pontic'])->default('single');
            $table->enum('finishing_stage', ['direct', 'biscuit', 'metallic'])->default('direct');
            $table->decimal('unit_price', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
