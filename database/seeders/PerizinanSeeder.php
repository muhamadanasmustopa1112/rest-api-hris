<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Perizinan;

class PerizinanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Perizinan::create([
            'jenis_izin_id'=> 2 ,
            'category_id' => 3,
            'companies_user_id' => 1,
            'tanggal_mulai' => Carbon::parse('2024-10-05'),
            'tanggal_selesai' => Carbon::parse('2024-10-08'),
            'jam_masuk' => Carbon::createFromTime(20, 30)->toTimeString(),
            'jam_keluar' => Carbon::createFromTime(20, 30)->toTimeString(),
            'keterangan' => 'Service Laptop',
            'status' => 'accept',
            'lampiran' => null,
        ]);

    }
}
