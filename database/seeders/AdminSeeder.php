<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;
use App\Models\School;
use Faker\Factory as Faker;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();  

        $schoolIds = School::pluck('id')->toArray();

        $adminNames = [
            'System',
            'Head',
            'Deputy',
            'Registrar'
        ];

        foreach ($adminNames as $name) {

            $email = strtolower($name) . ".admin@elimuhub.ac.ke";

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'first_name' => $name,
                    'last_name' => 'Admin',
                    'role' => 'admin',
                    'phone' => '07' . rand(10000000, 99999999),
                    'county' => 1,
                    'status' => 'active',
                    'password' => bcrypt('Secret123')
                ]
            );

            // Use faker instead of fake()
            $adminLevel = $faker->randomElement(['super_admin', 'school_admin']);

            // Assign NULL school if super_admin, otherwise random school
            $schoolId = $adminLevel === 'super_admin'
                ? null
                : $schoolIds[array_rand($schoolIds)];

            Admin::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'admin_level' => $adminLevel,
                    'school_id' => $schoolId,
                    'status' => 'active',
                ]
            );
        }
    }
}
