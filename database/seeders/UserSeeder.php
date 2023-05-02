<?php

namespace Database\Seeders;

use App\Models\Showroom;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            'name' => 'Admin',
            'email' => 'admin@haatbaazar.com',
            'password' => \Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now()
        ];

        $user = User::create($user);
        $user->showroom()->sync(DB::table('showrooms')->get()->pluck('id'));
    }
}
