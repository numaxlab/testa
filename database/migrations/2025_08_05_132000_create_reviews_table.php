<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Lunar\Base\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('product_id')
                ->constrained(config('lunar.database.table_prefix').'products')
                ->cascadeOnDelete();

            $table->json('quote');
            $table->string('author')->nullable();
            $table->string('media_name')->nullable();
            $table->string('link')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
