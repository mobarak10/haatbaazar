<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\LoanInstallment;
use Illuminate\Database\Seeder;

class LoanInstallmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Loan::all()->each(function (Loan $loan) {
            LoanInstallment::factory()
                ->count(5)
                ->create([
                    'loan_id' => $loan->id,
                ]);
        });
    }
}
