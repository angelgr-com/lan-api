<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 100)->nullable();
            $table->integer('chapter')->length(100)->nullable();
            $table->integer('paragraph')->length(1000)->nullable();
            $table->string('url', 255)->nullable();
            $table->uuid('author_id');

            $table->foreign('author_id')
            ->references('id')
            ->on('authors')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sources');
    }
};
