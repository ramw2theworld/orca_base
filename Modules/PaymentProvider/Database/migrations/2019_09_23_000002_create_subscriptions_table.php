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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->foreignId('payment_provider_id')->nullable();
            $table->string('name');
            $table->string('original_id');
            $table->enum('status', ['active', 'cancelled', 'completed', 'suspended', 'expired']);
            $table->string('price');
            $table->integer('quantity');
            $table->foreignId('plan_id')->constrained();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
