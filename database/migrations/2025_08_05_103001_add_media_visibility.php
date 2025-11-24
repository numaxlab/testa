<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('audios', function (Blueprint $table) {
            $table->string('visibility', 50)->default('public');
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->string('visibility', 50)->default('public');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->string('visibility', 50)->default('public');
        });
    }

    public function down(): void
    {
        Schema::table('audios', function (Blueprint $table) {
            $table->dropColumn('visibility');
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn('visibility');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('visibility');
        });
    }
};
