<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('content_taxonomy', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('content_id');
            $table->unsignedBigInteger('taxonomy_id');
            $table->integer('order')->default(0);
            $table->index('content_id');
            $table->index('taxonomy_id');
            $table->foreign('content_id')->references('id')->on('contents')->cascadeOnDelete();
            $table->foreign('taxonomy_id')->references('id')->on('taxonomies')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_taxonomy');
    }
};
