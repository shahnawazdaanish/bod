<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBioSkillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bio_skill', function (Blueprint $table) {
            $table->unsignedBigInteger('bio_id');
            $table->unsignedBigInteger('skill_id');
            $table->integer('knowledge_points');
            $table->boolean('active')->default(1);
            $table->boolean('show_in_profile')->default(1);
            $table->boolean('show_as_public')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['bio_id', 'skill_id']);
            $table->unique(['bio_id', 'skill_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bio_skill');
    }
}
