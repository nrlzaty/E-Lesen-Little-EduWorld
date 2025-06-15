<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bilangan_kanak_kanaks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('kod_id')->unsigned();
            $table->bigInteger('applicant_id')->unsigned();
            $table->foreign('kod_id')->references('id')->on('config_codes')->onDelete('cascade');
            $table->foreign('applicant_id')->references('id')->on('applicants')->onDelete('cascade');
            $table->string('bilangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bilangan_kanak_kanaks');
    }
};
