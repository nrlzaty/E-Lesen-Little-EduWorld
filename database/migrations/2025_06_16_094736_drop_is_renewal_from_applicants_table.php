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
    if (Schema::hasColumn('applicants', 'is_renewal')) {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn('is_renewal');
        });
    }
}
public function down()
{
    Schema::table('applicants', function (Blueprint $table) {
        $table->boolean('is_renewal')->default(false)->after('expiry_date');
    });
}
};