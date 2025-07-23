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
        Schema::create('rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Round 1, Semifinals, Finals, etc.
            $table->integer('round_number'); // Sequential round number (1, 2, 3, etc.)
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('total_groups')->default(1); // Number of groups in this round
            $table->json('settings')->nullable(); // Round-specific settings
            $table->boolean('is_elimination_round')->default(false); // Whether players are eliminated
            $table->integer('players_advance')->nullable(); // How many players advance from each group
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['competition_id', 'round_number']);
            $table->index(['competition_id', 'status']);
            $table->index('status');

            // Unique constraint for round numbers within a competition
            $table->unique(['competition_id', 'round_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rounds');
    }
};
