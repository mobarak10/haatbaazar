<?php

namespace Database\Seeders;

use App\Models\LoanAccount;
use Illuminate\Database\Seeder;

class LoanAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LoanAccount::factory()
            ->count(10)
            ->create();
    }
}
