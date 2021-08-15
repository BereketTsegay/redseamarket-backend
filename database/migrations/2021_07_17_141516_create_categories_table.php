<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('canonical_name');
            $table->longText('description');
            $table->string('image');
            // $table->integer('icon_class_id');
            $table->integer('display_flag')->default(0)->comment('1 disply in dashboard, 0 not display');
            $table->tinyInteger('status')->default(1);
            $table->integer('sort_order');
            $table->integer('delete_status')->default(0);
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
        Schema::dropIfExists('categories');
    }
}
