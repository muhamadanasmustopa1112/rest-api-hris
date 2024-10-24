<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



class Shift extends Model
{
    use HasFactory;

    protected $table = 'shift';

    protected $fillable = ['id','name','status', 'company_id'];

    public function jam(): HasMany
    {
        return $this->hasMany(Jam::class);
    }

    public function presensiMasuk(): HasMany
    {
        return $this->hasMany(PresensiMasuk::class, 'shift_id', 'id');
    }

    public function presensiKeluar(): HasMany
    {
        return $this->hasMany(PresensiKeluar::class, 'shift_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}
