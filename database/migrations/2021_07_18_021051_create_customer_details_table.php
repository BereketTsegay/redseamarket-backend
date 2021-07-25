<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_details', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('user_id');
            $table->text('address');
            $table->string('profile_picture');
            $table->string('email');
            $table->integer('country_code');
            $table->integer('city_code');
            $table->integer('currency_code');
            $table->string('mobile', 25);
            $table->string('gender', 70);
            $table->string('nationality');
            $table->date('date_of_birth');
            $table->boolean('email_notification_flag')->default(0)->comment('1 for notification and 0 for not');
            $table->integer('status');
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
        Schema::dropIfExists('customer_details');
    }
}
