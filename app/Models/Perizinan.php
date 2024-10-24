<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Perizinan extends Model
{
    use HasFactory;

    protected $table = 'perizinan';

    protected $fillable = [
        'id',
        'jenis_izin_id',
        'category_id',
        'companies_users_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'jam_masuk',
        'jam_keluar',
        'keterangan',
        'status',
        'lampiran',
    ];

    public function companyUser()
    {
        return $this->belongsTo(CompanyUser::class, 'companies_users_id');
    }

    public function jenisIzin()
    {
        return $this->belongsTo(JenisIzin::class , 'jenis_izin_id');
    }

    public function CategoryIzin()
    {
        return $this->belongsTo(CategoryIzin::class, 'category_id');
    }

}
