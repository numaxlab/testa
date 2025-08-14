<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->json('name');
            $table->json('subtitle')->nullable();
            $table->json('description')->nullable();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->string('delivery_method', 20);
            $table->string('location')->nullable();
            $table->boolean('is_published')->default(false);

            $table->foreignId('topic_id')->constrained('education_topics');

            $table->timestamps();
        });

        Schema::create('course_' . config('lunar.database.table_prefix') . 'product', function (Blueprint $table) {
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table
                ->foreignId('product_id')
                ->constrained(config('lunar.database.table_prefix') . 'products')
                ->cascadeOnDelete();
            $table->integer('position');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_' . config('lunar.database.table_prefix') . 'product');
        Schema::dropIfExists('courses');
    }
};
