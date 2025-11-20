<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Lunar\Base\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table
                ->foreignId('purchasable_id')
                ->after('topic_id')
                ->nullable()
                ->constrained($this->prefix.'products')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['purchasable_id']);
            $table->dropColumn('purchasable_id');
        });
    }
};
