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
        if (!Schema::hasTable('maklumat_pekerjas')) {
            Schema::create('maklumat_pekerjas', function (Blueprint $table) {
                $table->id();
                $table->string('applicant_id')->nullable();
                $table->string('nama_pekerja')->nullable();
                $table->string('kad_pengenalan_pekerja')->nullable();
                $table->string('umur_pekerja')->nullable();
                $table->string('jantina_pekerja')->nullable();
                $table->string('kelayakan_pekerja')->nullable();
                $table->string('jawatan_pekerja')->nullable();
                $table->date('tarikh_mula_pekerja')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maklumat_pekerjas');
    }
};