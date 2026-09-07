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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique();
            $table->string('sender_name');
            $table->string('sender_phone');
            $table->string('sender_address');
            $table->string('origin_city');
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->string('receiver_address');
            $table->string('destination_city');
            $table->decimal('weight_kg', 8, 2);
            $table->string('item_type');
            $table->enum('current_status', ['pending', 'received_at_warehouse', 'in_transit', 'out_for_delivery', 'delivered', 'failed'])->default('pending');
            $table->decimal('shipping_cost', 12, 2);
            $table->foreignId('current_warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->foreignId('assigned_courier_id')->nullable()->constrained('couriers')->nullOnDelete();
            $table->timestamps();

            $table->index('current_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
