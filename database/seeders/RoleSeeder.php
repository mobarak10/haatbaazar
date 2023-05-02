<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    private array $roles = [];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['Admin', 'Manager'];

        foreach ($roles as $role) {
            $r = [
                'name' => $role,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ];

            array_push($this->roles, $r);
        }

        Role::insert($this->roles);
    }
}
