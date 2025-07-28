<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DivisionPosition extends Model
{
    use HasFactory;

    protected $table = 'division_positions';

    protected $fillable = ['division_id', 'jabatan_id'];

    public static function ensureExists($divisionId, $jabatanId)
    {
        return self::firstOrCreate([
            'division_id' => $divisionId,
            'jabatan_id' => $jabatanId,
        ]);
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
