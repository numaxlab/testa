<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Lunar\Base\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->timestamps();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('venue_id')->after('location')->nullable()->constrained('venues');
            $table->dropColumn('location');
        });

        Schema::table('course_modules', function (Blueprint $table) {
            $table->foreignId('venue_id')->after('location')->nullable()->constrained('venues');
            $table->dropColumn('location');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venues');

        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['venue_id']);
            $table->string('location')->after('venue_id');
            $table->dropColumn('venue_id');
        });

        Schema::table('course_modules', function (Blueprint $table) {
            $table->dropForeign(['venue_id']);
            $table->string('location')->after('venue_id');
            $table->dropColumn('venue_id');
        });
    }
};
