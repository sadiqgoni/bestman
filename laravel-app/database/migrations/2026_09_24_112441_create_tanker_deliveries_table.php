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
        Schema::create('tanker_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('tanker_plate_number');
            $table->string('supplier_name');
            $table->string('invoice_number');
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tank_id')->constrained()->cascadeOnDelete();
            $table->decimal('waybill_liters', 12, 2);
            $table->decimal('received_liters', 12, 2);
            $table->decimal('variance_liters', 12, 2);
            $table->decimal('buying_price_per_liter', 10, 2)->nullable();
            $table->decimal('total_cost', 14, 2)->nullable();
            $table->text('driver_notes')->nullable();
            $table->string('status')->default('PENDING');
            $table->foreignId('created_by_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('confirmed_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanker_deliveries');
    }
};
