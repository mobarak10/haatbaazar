<?php

namespace Database\Seeders;

use App\Models\Party;
use Database\Factories\PartyFactory;
use Illuminate\Database\Seeder;

class PartySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Party::factory()
            ->count(100)
            ->create();
    }
}
