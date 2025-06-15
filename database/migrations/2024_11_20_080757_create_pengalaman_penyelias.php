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
        Schema::create('pengalaman_penyelias', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('kod_id')->unsigned();
            $table->bigInteger('penyelia_id')->unsigned();
            $table->foreign('kod_id')->references('id')->on('config_codes')->onDelete('cascade');
            $table->foreign('penyelia_id')->references('id')->on('butiran_penyelias')->onDelete('cascade');
            $table->boolean('is_true')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengalaman_penyelias');
    }
};
