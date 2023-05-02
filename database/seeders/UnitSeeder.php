<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Unit::create([
            'name' => 'Pcs',
            'code' => '0002',
            'label' => 'Pcs',
            'relation' => '1',
            'description' => '1 Pcs',
        ]);
    }
}
