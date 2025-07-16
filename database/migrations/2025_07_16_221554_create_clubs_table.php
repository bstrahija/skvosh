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
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();

            // Address information
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('state_province')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country', 2)->default('US'); // ISO 3166-1 alpha-2

            // Geographic data
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Club settings
            $table->boolean('is_active')->default(true);
            $table->json('operating_hours')->nullable(); // Store weekly schedule
            $table->json('amenities')->nullable(); // Store available amenities
            $table->json('sports')->nullable(); // Store supported sports

            // Images
            $table->string('logo_path')->nullable();
            $table->json('gallery_images')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['latitude', 'longitude']);
            $table->index('is_active');
            $table->index('country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};
