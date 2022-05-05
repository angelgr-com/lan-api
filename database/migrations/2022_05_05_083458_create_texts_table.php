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
        Schema::create('texts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('text', 65534)->unique();
            $table->uuid('author_id');
            $table->uuid('cefr_id');
            $table->uuid('difficulty_id');
            $table->uuid('type_id');
            $table->timestamps();

            $table->foreign('author_id')
                  ->references('id')
                  ->on('authors')
                  ->onDelete('cascade');
            $table->foreign('cefr_id')
                ->references('id')
                ->on('cefrs')
                ->onDelete('cascade');
            $table->foreign('difficulty_id')
                  ->references('id')
                  ->on('difficulties')
                  ->onDelete('cascade');
            $table->foreign('type_id')
                  ->references('id')
                  ->on('types')
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
        Schema::dropIfExists('texts');
    }
};
