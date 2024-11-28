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
        Schema::create('starred_repositories', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->unsignedBigInteger('repository_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('url');
            $table->string('language')->nullable();
            $table->timestamps();

            $table->unique(['repository_id', 'username']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('starred_repositories');
    }
};
