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
        Schema::table('butiran_penyelias', function (Blueprint $table) {
            $table->string('jenis_pengalaman_penyelia')->nullable()->after('kelulusan_akademik_penyelia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('butiran_penyelias', function (Blueprint $table) {
            $table->dropColumn('jenis_pengalaman_penyelia');
        });
    }
};
