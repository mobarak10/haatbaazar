<?php

namespace Database\Seeders;

use App\Models\Showroom;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShowroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Showroom::create([
            'name' => 'Mymensingh Showroom',
            'address' => 'Mymensingh',
            'comment' => '',
        ]);
    }
}
