<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    use HasFactory;

    protected $table = 'lembur';

    protected $fillable = [
        'id',
        'companies_users_id',
        'tanggal',
        'jam',
        'jam_keluar',
        'total_jam',
        'description',
        'status',
    ];

    public function companyUser()
    {
        return $this->belongsTo(CompanyUser::class, 'companies_users_id');
    }
}
