<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ShowroomSeeder::class,
            UserSeeder::class,
            MenuSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            AssignRolePermissionSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            UnitSeeder::class,
            ProductSeeder::class,
            StockSeeder::class,
            PartySeeder::class,
        ]);
        // \App\Models\User::factory(10)->create();
    }
}
