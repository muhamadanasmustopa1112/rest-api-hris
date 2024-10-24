<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class PresensiMasuk extends Model
{
    use HasFactory;

    protected $table = 'presensi_masuk';

    protected $fillable = [
        'id',
        'shift_id',
        'companies_users_id',
        'tanggal',
        'jam',
        'latitude',
        'longtitude',
        'status',
        'keteragan',
    ];

    public function companyUser()
    {
        return $this->belongsTo(CompanyUser::class, 'companies_users_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class , 'shift_id');
    }
}
