<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
       Schema::table('categories', function (Blueprint $table) {
        $table->boolean('type')->comment('0 for flat and 1 for percentage')->nullable();
         $table->double('percentage')->comment('applicable for featurd ads')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('type');
        Schema::dropIfExists('percentage');
    }
}
