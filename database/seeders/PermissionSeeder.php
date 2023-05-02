<?php

namespace Database\Seeders;

use App\Services\PermissionService\PermissionService;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        (new PermissionService())->generateAllPermissions();
    }
}
