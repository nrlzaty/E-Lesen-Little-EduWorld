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
        Schema::create('butiran_taskas', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_id')->nullable();
            $table->string('nama_taska')->nullable();
            $table->longText('alamat_taska')->nullable();
            $table->string('telefon_taska_r')->nullable();
            $table->string('telefon_taska_b')->nullable();
            $table->string('telefon_taska_p')->nullable();
            $table->string('faks_taska')->nullable();
            $table->string('email_taska')->nullable();
            $table->string('laman_web_taska')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('butiran_taskas');
    }
};
