<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRenewLicensesTable extends Migration
{
   public function up()
{
    Schema::create('renew_licenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('applicant_id');
            $table->unsignedBigInteger('previous_applicant_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            // $table->foreign('applicant_id')->references('id')->on('applicants');
            // $table->foreign('previous_applicant_id')->references('id')->on('applicants');
            $table->foreign('user_id')->references('id')->on('users');
        });
    
}

    public function down()
    {
        Schema::dropIfExists('renew_licenses');
    }
}
