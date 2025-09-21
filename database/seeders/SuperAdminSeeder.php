<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Prevent duplicate super admin creation
        if (!User::where('role', User::ROLE_SUPER_ADMIN)->exists()) {
            User::create([
                'pharmacy_id' => null, // Super Admin not tied to a pharmacy
                'username'    => 'RobertTheSuper',
                'full_name'   => 'Robert S, Nuru',
                'email'       => 'rstephano600@gmail.com',
                'phone'       => '0657856790',
                'password'    => Hash::make('RobertSuper4321@'), // change this after seeding
                'role'        => User::ROLE_SUPER_ADMIN,
                'is_active'   => true,
            ]);
        }
    }
}
