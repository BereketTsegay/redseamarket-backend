<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubcategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcategories', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id');
            $table->integer('parent_id')->default(0)->comment('without a parent default 0');
            $table->string('name');
            $table->string('canonical_name');
            $table->string('image');
            $table->longText('description');
            $table->boolean('type')->comment('0 for flat and 1 for percentage');
            $table->double('percentage')->comment('applicable for featurd ads');
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
        Schema::dropIfExists('subcategories');
    }
}
