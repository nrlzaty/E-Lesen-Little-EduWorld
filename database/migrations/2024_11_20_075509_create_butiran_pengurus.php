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
        Schema::create('butiran_pengurus', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_id')->nullable();
            $table->string('nama_pengurus')->nullable();
            $table->string('kad_pengenalan_pengurus')->nullable();
            $table->string('umur_pengurus')->nullable();
            $table->string('kelulusan_akademik_pengurus')->nullable();
            $table->string('jawatan_hakiki_pengurus')->nullable();
            $table->string('jenis_pengalaman_pengurus')->nullable();

        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('butiran_pengurus');
    }
};
