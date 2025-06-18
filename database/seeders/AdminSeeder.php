<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class adminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    
        public function run(): void
    {
        if (DB::table('users')->where('email', 'iansopian@example.com')->doesntExist()) {
            DB::table('users')->insert([
                'first_name' => 'Ian',
                'last_name' => 'Sopian',
                'email' => 'iansopian@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('H2H@carmen'),
                'phone' => '08123456789',
                'role' => 'admin',
                'remember_token' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    
    }
}