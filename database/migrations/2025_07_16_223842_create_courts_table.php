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
        Schema::create('courts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Court 1, Court 2, etc.
            $table->string('type')->default('squash'); // squash, tennis, badminton, etc.
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('hourly_rate', 8, 2)->nullable(); // Cost per hour
            $table->json('amenities')->nullable(); // Court-specific amenities
            $table->json('available_hours')->nullable(); // Court-specific availability
            $table->integer('max_players')->default(2); // Maximum players allowed
            $table->string('surface_type')->nullable(); // Wood, synthetic, etc.
            $table->text('equipment_included')->nullable(); // Racquets, balls, etc.
            $table->text('notes')->nullable(); // Special notes or instructions
            $table->integer('sort_order')->default(0); // For ordering courts
            $table->timestamps();

            // Indexes
            $table->index(['club_id', 'is_active']);
            $table->index(['club_id', 'type']);
            $table->index('sort_order');

            // Unique constraint to prevent duplicate court names within a club
            $table->unique(['club_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courts');
    }
};
