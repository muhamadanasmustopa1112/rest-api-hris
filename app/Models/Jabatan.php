<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    protected $table = 'jabatan';

    protected $fillable = ['name', 'company_id'];

    public function comapanies_users()
    {
        return $this->hasMany(CompanyUser::class);
    }
}
