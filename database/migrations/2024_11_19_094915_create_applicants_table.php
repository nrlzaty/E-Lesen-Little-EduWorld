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
        Schema::create('applicants', function (Blueprint $table) {
            // 1.BUTIRAN PEMOHON
            $table->id();
            $table->string('nama');
            $table->string('kad_pengenalan');
            $table->string('warganegara');
            $table->longText('alamat_rumah');
            $table->longText('alamat_berlainan');
            $table->string('email');
            $table->string('telefon_r');
            $table->string('telefon_b');
            $table->string('telefon_p');
            $table->string('faks');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
