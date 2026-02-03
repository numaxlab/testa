<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Lunar\Base\Migration;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('sort_position')->default(0);
            $table->string('type');
            $table->string('link_value')->nullable();
            $table->nullableMorphs('linkable');
            $table->boolean('is_published')->default(false);

            $table->timestamps();

            $table
                ->foreign('parent_id')
                ->references('id')->on('menu_items')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
