<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisIzin extends Model
{
    use HasFactory;

    protected $table = 'jenis_izin';

    protected $fillable = ['name'];

    public function perizinan(): HasMany
    {
        return $this->hasMany(Perizinan::class);
    }

    // public function comapanies_users()
    // {
    //     return $this->hasMany(CompanyUser::class);
    // }
}
