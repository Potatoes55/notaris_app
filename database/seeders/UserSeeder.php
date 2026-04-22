<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'notaris_id' => 1,
            'username'   => 'admin_john',
            'access_code' => 'admin',
            'email'      => 'admin@gmail.com',
            'password'   => 'secret',
            'phone'      => '081234567890',
            'address'    => '456 Main St, Cityville',
            'signup_at'  => now(),
            'active_at'  => now()->addDay(30),
            'status'     => 'active',
        ]);

        User::create([
            'notaris_id' => 2,
            'username'   => 'admin_maria',
            'email'      => 'admin2@gmail.com',
            'password'   => 'secret',
            'phone'      => '081234567890',
            'address'    => '456 Main St, Cityville',
            'signup_at'  => now(),
            'active_at'  => now()->addDay(15),
            'status'     => 'active',
        ]);

        User::create([
            'notaris_id' => 3,
            'username'   => 'Admin 3',
            'email'      => 'admin3@gmail.com',
            'password'   => 'secret',
            'phone'      => '081234567890',
            'address'    => '456 Main St, Cityville',
            'signup_at'  => now()->subDay(),
            'active_at'  => now()->subDay(),
            'status'     => 'pending',
        ]);
    }
}
