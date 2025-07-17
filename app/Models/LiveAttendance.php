<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveAttendance extends Model
{
    use HasFactory;

    protected $table = 'live_attendance_dinas';
    protected $fillable = [
        'id',
        'companies_users_id',
        'perjalanan_dinas_id',
        'tanggal',
        'jam_masuk',
        'latitude_masuk',
        'longtitude_masuk',
        'keteragan_masuk',
        'jam_keluar',
        'latitude_keluar',
        'longtitude_keluar',
        'keteragan_keluar',
    ];

    public function companyUser()
    {
        return $this->belongsTo(CompanyUser::class, 'companies_users_id');
    }

    public function perjalananDinas()
    {
        return $this->belongsTo(perjalananDinas::class, 'perjalanan_dinas_id');
    }
}


