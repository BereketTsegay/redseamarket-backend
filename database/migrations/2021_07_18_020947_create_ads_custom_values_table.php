<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdsCustomValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads_custom_values', function (Blueprint $table) {
            $table->id();
            $table->integer('ads_id');
            $table->integer('field_id');
            $table->integer('option_id');
            $table->text('value');
            $table->boolean('file')->default(0);
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
        Schema::dropIfExists('ads_custom_values');
    }
}
