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
        Schema::create('companies_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('division_id')->constrained('division')->onDelete('cascade');
            $table->foreignId('jabatan_id')->constrained('division')->onDelete('cascade');
            $table->string('nik')->unique()->nullable();
            $table->string('no_kk')->unique()->nullable();
            $table->string('name');
            $table->string('gender');
            $table->date('tgl_lahir');
            $table->string('tempat_lahir');
            $table->string('no_hp');
            $table->string('email')->unique();
            $table->string('no_hp_darurat');
            $table->string('status');
            $table->longText('alamat');
            $table->string('bpjs_kesehatan')->nullable();
            $table->string('bpjs_ketenagakerjaan')->nullable();
            $table->string('npwp')->nullable();
            $table->string('foto_karyawan')->nullable();
            $table->string('ktp_karyawan')->nullable();
            $table->string('ijazah_karyawan')->nullable();
            $table->string('qr_code')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies_users');
    }
};
