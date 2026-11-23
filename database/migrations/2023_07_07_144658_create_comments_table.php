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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('content_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('author_name')->nullable();
            $table->string('author_email')->nullable();
            $table->string('author_url')->nullable();
            $table->string('author_ip')->nullable();
            $table->text('comment')->nullable();
            $table->enum('approved', ['Y', 'N'])->default('Y');
            $table->string('type')->default('COMMENT')->nullable();
            $table->integer('parent')->default(0);
            $table->timestamps();
            $table->index('content_id');
            $table->index(['type', 'parent']);
            $table->foreign('content_id')->references('id')->on('contents')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
