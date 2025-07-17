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
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['tournament', 'league', 'single_match', 'ladder', 'round_robin'])->default('tournament');
            $table->enum('format', ['single_elimination', 'double_elimination', 'round_robin', 'swiss', 'ladder', 'league'])->default('single_elimination');
            $table->string('sport')->default('squash');
            $table->enum('status', ['draft', 'open', 'in_progress', 'completed', 'cancelled'])->default('draft');
            $table->boolean('is_ranked')->default(false);
            $table->boolean('is_public')->default(true);
            $table->integer('max_participants')->nullable();
            $table->integer('min_participants')->default(2);
            $table->decimal('entry_fee', 8, 2)->nullable();
            $table->json('prize_structure')->nullable(); // 1st, 2nd, 3rd place prizes
            $table->date('registration_start')->nullable();
            $table->date('registration_end')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('rules')->nullable(); // Competition-specific rules
            $table->json('settings')->nullable(); // Additional settings (match duration, scoring, etc.)
            $table->text('requirements')->nullable(); // Skill level, membership requirements, etc.
            $table->string('image_path')->nullable();
            $table->boolean('auto_schedule')->default(false); // Auto-schedule matches
            $table->integer('rounds_completed')->default(0);
            $table->integer('total_rounds')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['club_id', 'status']);
            $table->index(['type', 'status']);
            $table->index(['sport', 'status']);
            $table->index(['is_public', 'status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
