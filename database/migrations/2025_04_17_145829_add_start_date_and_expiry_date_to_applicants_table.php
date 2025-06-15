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
            $table->date('start_date')->nullable()->after('id'); // Replace 'id' with an actual column name
            $table->date('expiry_date')->nullable()->after('start_date');
        });
    } 
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'expiry_date']);
        });
    }
};
