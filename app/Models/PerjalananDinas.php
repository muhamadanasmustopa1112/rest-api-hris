<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerjalananDinas extends Model
{
    use HasFactory;

    protected $table = 'perjalanan_dinas';

    protected $fillable = [
        'company_id',
        'companies_users_id',
        'tanggal_berangkat',
        'tanggal_pulang',
        'tujuan',
        'keperluan',
        'status',
        'rejected_reason',
    ];

    /**
     * Relasi ke Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relasi ke user perusahaan (pegawai)
     */
    public function employee()
    {
        return $this->belongsTo(CompanyUser::class, 'companies_users_id');
    }
}
