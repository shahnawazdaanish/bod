<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEducationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('education', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('bio_id');
            $table->string('institute_name');
            $table->string('institute_address')->nullable();
            $table->string('institute_country')->nullable();
            $table->string('name_of_certification');
            $table->year('study_year_from')->nullable();
            $table->year('study_year_to')->nullable();
            $table->boolean('currently_studying')->default(0);
            $table->string('subject')->nullable();
            $table->string('major')->nullable();
            $table->enum('result_based_on', ['grade', 'cgpa', 'class', 'others'])->nullable();
            $table->string('accumulated_result')->nullable();
            $table->string('accumulated_out_of')->nullable();
            $table->longText('details')->nullable();
            $table->boolean('active')->default(1);
            $table->boolean('show_in_profile')->default(1);
            $table->boolean('show_as_public')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('bio_id')->references('id')->on('bios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('education');
    }
}
