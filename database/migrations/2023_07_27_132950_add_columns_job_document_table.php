<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsJobDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_documents', function (Blueprint $table) {
            $table->string('total_experience')->nullable();
            $table->string('current_ctc')->nullable();
            $table->string('expect_ctc')->nullable();
            $table->string('notice_period')->nullable();
            $table->string('relevent_field')->nullable();
            $table->string('current_company')->nullable();
            $table->string('cv_file')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
