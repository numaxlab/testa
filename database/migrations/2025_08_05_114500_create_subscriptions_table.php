<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Lunar\Base\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')->constrained($this->prefix.'customers');
            $table->foreignId('membership_plan_id')->constrained('membership_plans');
            $table->string('status', 20);
            $table->date('started_at');
            $table->date('expires_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
