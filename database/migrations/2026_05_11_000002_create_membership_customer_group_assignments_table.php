<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_customer_group_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('subscription_id');
            $table->unsignedBigInteger('benefit_id');
            $table->unsignedBigInteger('customer_group_id');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')
                ->on(config('lunar.database.table_prefix').'customers')
                ->cascadeOnDelete();

            $table->foreign('subscription_id')
                ->references('id')
                ->on('subscriptions')
                ->cascadeOnDelete();

            $table->foreign('benefit_id')
                ->references('id')
                ->on('benefits')
                ->cascadeOnDelete();

            $table->foreign('customer_group_id')
                ->references('id')
                ->on(config('lunar.database.table_prefix').'customer_groups')
                ->cascadeOnDelete();

            $table->unique(
                ['customer_id', 'subscription_id', 'benefit_id', 'customer_group_id'],
                'membership_cg_assignments_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_customer_group_assignments');
    }
};
