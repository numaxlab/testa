<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Lunar\Base\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('membership_plans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('membership_tier_id')->constrained('membership_tiers');
            $table->unsignedBigInteger('tax_class_id')->nullable();
            $table->foreign('tax_class_id')->references('id')->on($this->prefix.'tax_classes');

            $table->json('name');
            $table->json('description')->nullable();
            $table->string('billing_interval')->nullable();
            $table->boolean('is_published')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_plans');
    }
};
