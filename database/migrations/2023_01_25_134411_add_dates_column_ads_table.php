<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDatesColumnAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->timestamp('accept_at')->nullable()->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('start_at')->nullable()->default(\DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ads', function (Blueprint $table) {
            Schema::dropIfExists('accept_at');
            Schema::dropIfExists('start_at');
        });
    }
}
