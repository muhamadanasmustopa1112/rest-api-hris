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
        Schema::create('presensi_masuk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained('shift')->onDelete('cascade');
            $table->foreignId('companies_users_id')->constrained('companies_users')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam');
            $table->string('latitude');
            $table->string('longtitude');
            $table->string('status');
            $table->string('keteragan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi_masuk');
    }
};
