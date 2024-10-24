<?php

namespace App\Models;
use App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyUser extends Model
{
    use HasFactory;

    protected $table = 'companies_users';

    protected $fillable = [
        'id',
        'nik',
        'no_kk',
        'name',
        'gender',
        'tgl_lahir',
        'tempat_lahir',
        'no_hp',
        'email',
        'no_hp_darurat',
        'status',
        'alamat',
        'bpjs_kesehatan',
        'bpjs_ketenagakerjaan',
        'npwp',
        'foto_karyawan',
        'ktp_karyawan',
        'ijazah_karyawan',
        'qr_code',
        'company_id',
        'division_id',
        'jabatan_id',
    ];

    public function perizinan(): HasMany
    {
        return $this->hasMany(Perizinan::class);
    }

    public function lembur(): HasMany
    {
        return $this->hasMany(Lembur::class);
    }

    public function kasbon(): HasMany
    {
        return $this->hasMany(Kasbon::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }
}
