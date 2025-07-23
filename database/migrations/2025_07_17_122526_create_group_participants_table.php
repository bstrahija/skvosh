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
        Schema::create('group_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('position')->nullable(); // Position within the group (1st, 2nd, etc.)
            $table->decimal('seed', 5, 2)->nullable(); // Seeding within this group
            $table->integer('points')->default(0); // Points earned in this group
            $table->integer('wins')->default(0); // Wins in this group
            $table->integer('losses')->default(0); // Losses in this group
            $table->integer('draws')->default(0); // Draws in this group
            $table->json('statistics')->nullable(); // Detailed statistics for this group
            $table->boolean('advanced')->default(false); // Whether player advanced from this group
            $table->boolean('eliminated')->default(false); // Whether player was eliminated
            $table->timestamp('joined_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Unique constraint - user can only be in a group once
            $table->unique(['group_id', 'user_id']);

            // Indexes
            $table->index(['group_id', 'position']);
            $table->index(['group_id', 'points']);
            $table->index(['user_id', 'advanced']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_participants');
    }
};
