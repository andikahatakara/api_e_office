<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $users = collect([
            [
                'first_name' => 'Emon',
                'last_name' => 'Krismon',
                'email' => 'emonkrismon98@gmail.com',
                'password' => '$2y$10$du7Z5gVR2z43Z6Am4Fe1J.Q6FAiTokds2yUcdWHb68muqZ1x0Re9W',
                'nip' => '12345678',
                'avatar' => 'avatars/default.png',
                'email_verified_at' => now()
            ],
        ]);

        /**
         * Only create users on development mode.
        */
        if(config('app.env') === 'local') {
            $operator = [
                'first_name' => 'Operator TU',
                'last_name' => 'Testing',
                'email' => 'operatortesting@gmail.com',
                'password' => '$2y$10$du7Z5gVR2z43Z6Am4Fe1J.Q6FAiTokds2yUcdWHb68muqZ1x0Re9W',
                'nip' => '12345678',
                'avatar' => 'avatars/default.png',
                'email_verified_at' => now()
            ];
            $employee = [
                'first_name' => 'Pegawai',
                'last_name' => 'Testing',
                'email' => 'pegawaitesting@gmail.com',
                'password' => '$2y$10$du7Z5gVR2z43Z6Am4Fe1J.Q6FAiTokds2yUcdWHb68muqZ1x0Re9W',
                'nip' => '12345678',
                'avatar' => 'avatars/default.png',
                'email_verified_at' => now()
            ];

            $users->push($operator, $employee);
        }

        // define use as super admin
        $users->map(function($user) {
            User::create($user);
        });


    }
}
