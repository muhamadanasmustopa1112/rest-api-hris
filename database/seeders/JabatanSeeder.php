<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Jabatan;


class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Jabatan::create(['name' => 'Manager', 'company_id' => 1]);
        Jabatan::create(['name' => 'Staff' , 'company_id' => 1]);
        Jabatan::create(['name' => 'Staff Magang', 'company_id' => 1]);
    }
}
