<?php

namespace Database\Seeders;

use App\Models\CostCategory;
use App\Models\CostSubcategory;
use App\Models\Expense;
use Database\Factories\ExpenseFactory;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CostSubcategory::get()->each(function (CostSubcategory $cost_subcategory) {
            Expense::factory()->count(10)->create([
                'cost_subcategory_id' => $cost_subcategory->id
            ]);
        });
    }
}
