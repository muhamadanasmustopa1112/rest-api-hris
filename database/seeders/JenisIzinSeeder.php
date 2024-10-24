<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JenisIzin;

class JenisIzinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        JenisIzin::create(['name' => 'Jam']);
        JenisIzin::create(['name' => 'Harian']);
    }
}
