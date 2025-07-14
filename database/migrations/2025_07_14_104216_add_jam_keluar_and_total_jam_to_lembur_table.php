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
        Schema::table('lembur', function (Blueprint $table) {
            $table->time('jam_keluar')->after('jam');
            $table->decimal('total_jam', 5, 2)->after('jam_keluar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lembur', function (Blueprint $table) {
            $table->dropColumn(['jam_keluar', 'total_jam']);
        });
    }
};
