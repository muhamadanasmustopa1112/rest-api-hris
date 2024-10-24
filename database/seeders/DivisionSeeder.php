<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Division;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Division::create(['name' => 'Keuangan', 'company_id' => 1]);
        Division::create(['name' => 'HRD' , 'company_id' => 1]);
        Division::create(['name' => 'Administrasi', 'company_id' => 1]);
        Division::create(['name' => 'IT', 'company_id' => 1]);
    }
}
