<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('slides', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('image')->nullable();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->string('image')->nullable();
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->string('image')->nullable();
        });

        Schema::table('slides', function (Blueprint $table) {
            $table->string('image')->nullable();
        });
    }
};
