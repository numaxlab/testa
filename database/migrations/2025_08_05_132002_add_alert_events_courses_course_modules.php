<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Lunar\Base\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->json('alert')->nullable();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->json('alert')->nullable();
        });

        Schema::table('course_modules', function (Blueprint $table) {
            $table->json('alert')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('alert');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('alert');
        });

        Schema::table('course_modules', function (Blueprint $table) {
            $table->dropColumn('alert');
        });
    }
};
