<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Lunar\Base\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('image')->after('location');
            $table->string('register_url')->after('image')->nullable();
        });

        Schema::create(
            'event_'.config('lunar.database.table_prefix').'geslib_author',
            function (Blueprint $table) {
                $table
                    ->foreignId('event_id')
                    ->constrained('events')
                    ->cascadeOnDelete();
                $table
                    ->foreignId('author_id')
                    ->constrained(config('lunar.database.table_prefix').'geslib_authors')
                    ->cascadeOnDelete();
                $table->integer('position')->default(0);
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('event_'.config('lunar.database.table_prefix').'geslib_author');

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->dropColumn('register_url');
        });
    }
};
