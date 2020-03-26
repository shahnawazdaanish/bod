<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_contents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('content_id')->unique();
            $table->string('title')->nullable();
            $table->enum('type', ['image', 'video', 'animation', 'file', 'audio']);
            $table->boolean('active')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_contents');
    }
}
