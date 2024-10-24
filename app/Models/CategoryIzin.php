<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryIzin extends Model
{
    use HasFactory;

    protected $table = 'category_izin';

    protected $fillable = ['name'];

    public function perizinan(): HasMany
    {
        return $this->hasMany(Perizinan::class);
    }
    
    // public function comapanies_users()
    // {
    //     return $this->hasMany(CompanyUser::class);
    // }

    // // Relasi dengan model Company
    //     public function company()
    // {
    //     return $this->belongsTo(Company::class);
    // }
}
