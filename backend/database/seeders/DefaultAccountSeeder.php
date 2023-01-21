<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'first_name' => 'Adrian',
            'last_name' => 'Villanueva',
            'address' => 'Brgy. 109-A Solar Alley, Phase 2B V&G Tacloban City, Leyte',
            'position' => 'Coach',
            'user_type' => 'admin',
            'gender' => 'Male',
            'email' => 'avillanueva@gmail.com',
            'password' => '123123',
            'height' => 175,
            'weight' => 60,
            'approved_at' => now(),
            'email_verified_at' => now()
        ]);
    }
}
