<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment_image', function (Blueprint $table) {
            $table->unsignedBigInteger('id_comment');
            $table->foreign('id_comment')->references('id')->on('comments')->onDelete('cascade');
            $table->unsignedBigInteger('id_image');
            $table->foreign('id_image')->references('id')->on('images')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comment_image');
    }
}
