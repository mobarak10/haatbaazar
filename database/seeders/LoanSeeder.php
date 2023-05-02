<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\LoanAccount;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LoanAccount::all()->each(function (LoanAccount $loan_account) {
            Loan::factory()
                ->count(5)
                ->create([
                    'loan_account_id' => $loan_account->id,
                ]);
        });
    }
}
