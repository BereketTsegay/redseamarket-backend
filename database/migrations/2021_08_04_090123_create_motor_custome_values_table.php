<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMotorCustomeValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motor_custome_values', function (Blueprint $table) {
            $table->id();
            $table->integer('ads_id');
            $table->integer('make_id');
            $table->integer('model_id');
            $table->integer('varient_id');
            $table->string('registration_year');
            $table->string('fuel_type');
            $table->string('transmission');
            $table->string('condition');
            $table->integer('milage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('motor_custome_values');
    }
}
