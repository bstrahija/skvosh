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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->onDelete('cascade');
            $table->foreignId('round_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Group A, Group B, Pool 1, etc.
            $table->integer('group_number'); // Sequential group number within round (1, 2, 3, etc.)
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');
            $table->integer('max_players')->default(4); // Maximum players in this group
            $table->integer('current_players')->default(0); // Current number of players
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('settings')->nullable(); // Group-specific settings
            $table->json('standings')->nullable(); // Current standings/results
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['competition_id', 'round_id']);
            $table->index(['round_id', 'group_number']);
            $table->index(['competition_id', 'status']);
            $table->index('status');

            // Unique constraint for group numbers within a round
            $table->unique(['round_id', 'group_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
