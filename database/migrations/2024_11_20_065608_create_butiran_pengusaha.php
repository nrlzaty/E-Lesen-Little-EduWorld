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
        Schema::create('butiran_pengusahas', function (Blueprint $table) {
            $table->id();
            // $table->string('applicant_id')->nullable();
            $table->unsignedBigInteger('applicant_id')->nullable();
            $table->foreign('applicant_id')->references('id')->on('applicants')->onDelete('cascade');
            

            //2.BUTIRAN PENGUSAHA (A. Sekiranya individu)
            $table->string('nama_pengusaha')->nullable();
            $table->string('kad_pengenalan_pengusaha')->nullable();
            $table->string('warganegara_pengusaha')->nullable();
            $table->longText('alamat_rumah_pengusaha')->nullable();
            $table->longText('alamat_berlainan_pengusaha')->nullable();
            $table->string('email_pengusaha')->nullable();
            $table->string('telefon_pengusaha')->nullable();
            $table->string('faks_pengusaha')->nullable();
            $table->string('jenis_pengusaha')->nullable();
           
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('butiran_pengusahas');
        Schema::table('butiran_pengusahas', function (Blueprint $table) {
            $table->dropForeign(['applicant_id']);
            $table->unsignedBigInteger('applicant_id')->nullable()->change();
        });
    }
};
