<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('education_topics', function (Blueprint $table) {
            $table->id();

            $table->json('name');
            $table->json('subtitle')->nullable();
            $table->json('description')->nullable();
            $table->boolean('is_published')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('education_topics');
    }
};
