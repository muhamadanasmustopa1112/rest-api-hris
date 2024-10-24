<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Kasbon extends Model
{
    use HasFactory;

    protected $table = 'kasbon';

    protected $fillable = [
        'id',
        'companies_users_id',
        'tanggal',
        'nominal',
        'tenor',
        'keterangan',
        'status',
    ];

    public function companyUser()
    {
        return $this->belongsTo(CompanyUser::class, 'companies_users_id');
    }
}
