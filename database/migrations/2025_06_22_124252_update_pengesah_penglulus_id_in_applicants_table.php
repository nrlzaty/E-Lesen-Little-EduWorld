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
        Schema::table('applicants', function (Blueprint $table) {
            $table->bigInteger('pengesah_id')->unsigned()->nullable()->change();
            $table->bigInteger('penglulus_id')->unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->bigInteger('pengesah_id')->unsigned()->nullable(false)->change();
            $table->bigInteger('penglulus_id')->unsigned()->nullable(false)->change();
        });
    }
};
          

  
