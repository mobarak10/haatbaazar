<?php

namespace Database\Seeders;

use App\Models\CostCategory;
use Illuminate\Database\Seeder;

class CostCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CostCategory::factory()
            ->count(10)
            ->hasCostSubcategories(5)
            ->create();
    }
}
