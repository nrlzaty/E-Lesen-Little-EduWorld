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
        Schema::create('config_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kod',10);
            $table->string('kod_lain',10)->nullable();
            $table->string('keterangan', 100);
            $table->string('kategori', 30);
            $table->boolean('is_aktif')->default(true);
            $table->boolean('soft_delete')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_codes');
    }
};
