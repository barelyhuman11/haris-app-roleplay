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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('slug');
            $table->text('title')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('status')->default('PUBLISH')->nullable();
            $table->string('comment_status')->default('OPEN')->nullable();
            $table->string('password')->nullable();
            $table->integer('parent')->default(0);
            $table->string('type')->default('POST')->nullable();
            $table->integer('comment_count')->default(0);
            $table->integer('publish_time')->nullable();
            $table->timestamps();
            $table->index('slug');
            $table->index(['type', 'parent']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
