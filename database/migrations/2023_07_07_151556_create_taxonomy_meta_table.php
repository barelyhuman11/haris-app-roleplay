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
        Schema::create('taxonomy_meta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('taxonomy_id');
            $table->string('key')->nullable();
            $table->longText('value')->nullable();
            $table->foreign('taxonomy_id')->references('id')->on('taxonomies')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxonomy_meta');
    }
};
