<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassModel;

class ClassModelSeeder extends Seeder
{
    public function run()
    {
        $managerId = 26;

        $classes = [
            // Pre-primary
            ['name' => 'PP1', 'level_group' => 'pre_primary'],
            ['name' => 'PP2', 'level_group' => 'pre_primary'],

            // Lower Primary
            ['name' => 'Grade 1', 'level_group' => 'lower_primary'],
            ['name' => 'Grade 2', 'level_group' => 'lower_primary'],
            ['name' => 'Grade 3', 'level_group' => 'lower_primary'],

            // Upper Primary
            ['name' => 'Grade 4', 'level_group' => 'upper_primary'],
            ['name' => 'Grade 5', 'level_group' => 'upper_primary'],
            ['name' => 'Grade 6', 'level_group' => 'upper_primary'],

            // Junior Secondary
            ['name' => 'Grade 7', 'level_group' => 'junior_secondary'],
            ['name' => 'Grade 8', 'level_group' => 'junior_secondary'],
            ['name' => 'Grade 9', 'level_group' => 'junior_secondary'],

            // Senior School
            ['name' => 'Grade 10', 'level_group' => 'senior_school'],
            ['name' => 'Grade 11', 'level_group' => 'senior_school'],
            ['name' => 'Grade 12', 'level_group' => 'senior_school'],
        ];

        foreach ($classes as $c) {
            ClassModel::firstOrCreate(
                ['name' => $c['name']],
                [
                    'level_group' => $c['level_group'],
                    'manager_id' => $managerId
                ]
            );
        }
    }
}
