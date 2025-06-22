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
        // Columns already exist, so skip adding them to avoid duplicate error
        // Schema::table('applicants', function (Blueprint $table) {
        //     $table->bigInteger('pengesah_id')->unsigned();
        //     $table->bigInteger('penglulus_id')->unsigned();
        //     $table->foreign('pengesah_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('penglulus_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->string('status')->default('Pemohonan Baru');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

