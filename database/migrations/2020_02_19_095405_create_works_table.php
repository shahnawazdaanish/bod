<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorksTable extends Migration
{
    protected $casts = [
        'contents' => 'array'
    ];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('works', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('bio_id');
            $table->string('work_title');
            $table->string('organization_name')->nullable();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->date('work_starts_from')->nullable();
            $table->date('work_ends_to')->nullable();
            $table->text('contents')->nullable();
            $table->longText('work_description')->nullable();            
            $table->boolean('active')->default(1);
            $table->boolean('show_in_profile')->default(1);
            $table->boolean('show_as_public')->default(0);
            $table->unsignedBigInteger('featured_content')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('bio_id')->references('id')->on('bios')->onDelete('cascade');
            $table->foreign('organization_id')->references('id')->on('organizations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('works');
    }
}
