<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users  =   [
            [
                'name'      =>  'Admin',
                'email'     =>  'admin@mail.io',
                'password'  =>  Hash::make('password'),
                'role'      =>  'admin',
                'email_verified_at' =>  now(),
            ],
            [
                'name'      =>  'Dosen',
                'email'     =>  'dosen@mail.io',
                'password'  =>  Hash::make('password'),
                'role'      =>  'dosen',
                'email_verified_at' =>  now(),
            ],
        ];

        foreach ($users as $user) {
            \App\Models\User::create($user);
        }
    }
}
