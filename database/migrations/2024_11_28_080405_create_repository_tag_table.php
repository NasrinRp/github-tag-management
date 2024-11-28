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
        Schema::create('repository_tag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('starred_repository_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();

            $table->foreign('starred_repository_id')->references('id')->on('starred_repositories')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');

            $table->unique(['starred_repository_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repository_tag');
    }
};
