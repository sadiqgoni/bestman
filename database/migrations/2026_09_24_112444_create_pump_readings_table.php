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
        Schema::create('pump_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pump_id')->constrained()->cascadeOnDelete();
            $table->decimal('opening_reading', 12, 2);
            $table->decimal('closing_reading', 12, 2);
            $table->decimal('liters_sold', 12, 2);
            $table->decimal('unit_selling_price', 10, 2);
            $table->decimal('total_amount', 14, 2);
            $table->decimal('base_price_at_entry', 10, 2);
            $table->boolean('price_variance')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pump_readings');
    }
};
