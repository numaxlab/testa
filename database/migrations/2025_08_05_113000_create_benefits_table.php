<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('benefits', function (Blueprint $table) {
            $table->id();

            $table->string('code');
            $table->json('name');

            $table->timestamps();
        });

        Schema::create('benefit_membership_plan', function (Blueprint $table) {
            $table->foreignId('benefit_id')->constrained('benefits');
            $table->foreignId('membership_plan_id')->constrained('membership_plans');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('benefit_membership_plan');
        Schema::dropIfExists('benefits');
    }
};
