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
        Schema::create('live_attendance_dinas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('companies_users_id')->constrained('companies_users')->onDelete('cascade');
            $table->foreignId('perjalanan_dinas_id')->constrained('perjalanan_dinas')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam_masuk');
            $table->string('latitude_masuk');
            $table->string('longtitude_masuk');
            $table->string('keteragan_masuk')->nullable();
            $table->time('jam_keluar')->nullable();
            $table->string('latitude_keluar')->nullable();
            $table->string('longtitude_keluar')->nullable();
            $table->string('keteragan_keluar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_attendance_dinas');
    }
};
