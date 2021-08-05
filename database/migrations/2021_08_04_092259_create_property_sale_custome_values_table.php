<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertySaleCustomeValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_sale_custome_values', function (Blueprint $table) {
            $table->id();
            $table->integer('ads_id');
            $table->integer('size');
            $table->integer('room');
            $table->string('furnished');
            $table->string('building_type');
            $table->boolean('parking');
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
        Schema::dropIfExists('property_sale_custome_values');
    }
}
