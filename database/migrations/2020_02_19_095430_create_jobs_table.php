<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('bio_id');
            $table->string('organization_name');
            $table->string('organization_address')->nullable();
            $table->string('organization_country')->nullable();
            $table->string('type_of_organization')->nullable();
            $table->string('job_title');
            $table->string('job_type')->nullable();
            $table->year('working_year_from')->nullable();
            $table->year('working_year_to')->nullable();
            $table->boolean('currently_working')->default(0);
            $table->string('department')->nullable();
            $table->string('division')->nullable();
            $table->longText('job_description')->nullable();
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
        Schema::dropIfExists('jobs');
    }
}
