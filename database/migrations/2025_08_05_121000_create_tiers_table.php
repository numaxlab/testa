<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Lunar\Base\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tiers', function (Blueprint $table) {
            $table->id();

            $table->json('name');
            $table->string('section');
            $table->string('type');
            $table->integer('sort_position')->default(0);
            $table->boolean('is_published')->default(false);

            $table->timestamps();
        });

        Schema::create('banner_tier', function (Blueprint $table) {
            $table->foreignId('tier_id')->constrained('tiers')->cascadeOnDelete();
            $table->foreignId('banner_id')->constrained('banners')->cascadeOnDelete();
        });

        Schema::create(config('lunar.database.table_prefix').'collection_tier', function (Blueprint $table) {
            $table->foreignId('tier_id')->constrained('tiers')->cascadeOnDelete();
            $table->foreignId('collection_id')->constrained(config('lunar.database.table_prefix').'collections')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('lunar.database.table_prefix').'collection_tier');
        Schema::dropIfExists('banner_tier');
        Schema::dropIfExists('tiers');
    }
};
