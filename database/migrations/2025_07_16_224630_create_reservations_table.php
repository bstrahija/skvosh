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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('court_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('reservation_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes'); // Calculated field for easy queries
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->integer('player_count')->default(1);
            $table->text('notes')->nullable();
            $table->json('additional_services')->nullable(); // Equipment rental, coaching, etc.
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['court_id', 'reservation_date', 'start_time']);
            $table->index(['user_id', 'reservation_date']);
            $table->index(['reservation_date', 'status']);
            $table->index('status');

            // Unique constraint to prevent double bookings
            $table->unique(['court_id', 'reservation_date', 'start_time', 'end_time'], 'unique_court_time_slot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
