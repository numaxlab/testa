<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Lunar\Base\Migration;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('membership_tiers', function (Blueprint $table) {
            $table
                ->foreignId('purchasable_id')
                ->nullable()
                ->constrained($this->prefix.'products')
                ->restrictOnDelete();
        });

        Schema::table('membership_plans', function (Blueprint $table) {
            $table
                ->foreignId('variant_id')
                ->nullable()
                ->constrained($this->prefix.'product_variants')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('variant_id');
        });

        Schema::table('membership_tiers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('purchasable_id');
        });
    }
};
