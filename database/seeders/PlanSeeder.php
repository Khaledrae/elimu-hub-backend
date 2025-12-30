<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        //
        // database/seeders/PlanSeeder.php
        Plan::insert([
            [
                'code' => 'premium_daily',
                'name' => 'Premium Daily',
                'amount' => 50,
                'duration_days' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'premium_monthly',
                'name' => 'Premium Monthly',
                'amount' => 500,
                'duration_days' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'premium_term',
                'name' => 'Premium Term',
                'amount' => 1500,
                'duration_days' => 120,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
