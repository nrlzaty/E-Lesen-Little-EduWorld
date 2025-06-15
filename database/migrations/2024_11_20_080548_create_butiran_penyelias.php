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
        Schema::create('butiran_penyelias', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_id')->nullable();
            $table->string('nama_penyelia')->nullable();
            $table->string('kad_pengenalan_penyelia')->nullable();
            $table->string('umur_penyelia')->nullable();
            $table->string('kelulusan_akademik_penyelia')->nullable();
            $table->string('jenis_pengalaman_penyelia')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('butiran_penyelias');
    }
};
