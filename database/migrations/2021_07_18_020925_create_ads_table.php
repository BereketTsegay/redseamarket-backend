<?php

use App\Common\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id')->default(0);
            $table->integer('subcategory_id')->default(0);
            $table->string('title')->nullable();
            $table->string('canonical_name');
            $table->text('description')->nullable();
            $table->double('price');
            $table->boolean('negotiable_flag');
            $table->integer('country_id');
            $table->integer('state_id');
            $table->integer('city_id');
            $table->integer('sellerinformation_id')->comment('0 for admin ads');
            $table->integer('customer_id')->comment('user id');
            $table->integer('payment_id')->default(0)->comment('non featured ads has no payment');
            $table->boolean('featured_flag')->default(0)->comment('for featured ads value 1');
            $table->decimal('latitude', 10, 7)->default(0);
            $table->decimal('longitude', 10, 7)->default(0);
            $table->integer('status')->default(Status::ACTIVE)->comment('for rejected ads status becom 2');
            $table->integer('reject_reason_id')->default(0);
            $table->boolean('delete_status')->default(0);
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
        Schema::dropIfExists('ads');
    }
}
