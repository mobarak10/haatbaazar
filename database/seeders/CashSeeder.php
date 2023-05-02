<?php

namespace Database\Seeders;

use App\Models\Cash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CashSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $title = 'Main Cash';
        Cash::create([
            'title' => $title,
            'showroom_id' => DB::table('showrooms')->first()->id,
            'slug' => Str::slug($title),
            'balance' => 5000,
        ]);
    }
}
