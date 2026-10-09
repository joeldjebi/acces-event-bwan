<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('role')->nullable();
            $table->longText('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('platform_application_solution', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('solution_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['platform_application_id', 'solution_id'], 'application_solution_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_application_solution');
        Schema::dropIfExists('platform_applications');
    }
};
