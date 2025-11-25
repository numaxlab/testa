<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Lunar\Base\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::table(config('lunar.database.table_prefix').'orders', function (Blueprint $table) {
            $table->boolean('was_redeemed')->default(false)->after('is_geslib');
        });
    }

    public function down(): void
    {
        Schema::table(config('lunar.database.table_prefix').'orders', function (Blueprint $table) {
            $table->dropColumn('was_redeemed');
        });
    }
};
