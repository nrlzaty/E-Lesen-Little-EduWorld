<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn('is_renewal');
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->boolean('is_renewal')->default(false)->after('expiry_date');
        });
    }
};
