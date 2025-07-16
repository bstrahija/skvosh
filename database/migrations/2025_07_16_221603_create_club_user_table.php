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
        Schema::create('club_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Role in the club
            $table->enum('role', ['member', 'admin', 'owner'])->default('member');

            // Membership details
            $table->date('joined_at');
            $table->date('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('permissions')->nullable(); // Store specific permissions for admins

            $table->timestamps();

            // Unique constraint to prevent duplicate memberships
            $table->unique(['club_id', 'user_id']);

            // Indexes
            $table->index('role');
            $table->index('is_active');
            $table->index('joined_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_user');
    }
};
