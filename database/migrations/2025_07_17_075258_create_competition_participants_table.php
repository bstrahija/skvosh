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
        Schema::create('competition_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['participant', 'admin', 'organizer'])->default('participant');
            $table->enum('status', ['registered', 'confirmed', 'withdrawn', 'eliminated', 'disqualified'])->default('registered');
            $table->decimal('seed', 5, 2)->nullable(); // Tournament seeding
            $table->integer('current_ranking')->nullable(); // Current position in competition
            $table->integer('points')->default(0); // Points earned in competition
            $table->integer('wins')->default(0);
            $table->integer('losses')->default(0);
            $table->integer('draws')->default(0);
            $table->json('statistics')->nullable(); // Additional stats (games won/lost, etc.)
            $table->decimal('entry_fee_paid', 8, 2)->nullable();
            $table->boolean('fee_paid')->default(false);
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->text('withdrawal_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Unique constraint - user can only participate once per competition
            $table->unique(['competition_id', 'user_id']);

            // Indexes
            $table->index(['competition_id', 'role']);
            $table->index(['competition_id', 'status']);
            $table->index(['competition_id', 'current_ranking']);
            $table->index('seed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_participants');
    }
};
