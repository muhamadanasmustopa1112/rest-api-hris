<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CategoryIzin;


class CategoryIzinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        CategoryIzin::create(['name' => 'Izin']);
        CategoryIzin::create(['name' => 'Cuti']);
        CategoryIzin::create(['name' => 'Sakit']);
    }
}
