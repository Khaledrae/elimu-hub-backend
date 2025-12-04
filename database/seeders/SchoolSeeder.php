<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;

class SchoolSeeder extends Seeder
{
    public function run()
    {
        $schools = [
            [
                'name' => 'ElimuHub School',
                'address' => 'Nairobi, Kenya',
                'contact_email' => 'info@elimuhub.ac.ke',
                'contact_phone' => '0700000001'
            ],
            [
                'name' => 'Green Valley Academy',
                'address' => 'Kiambu, Kenya',
                'contact_email' => 'contact@gva.ac.ke',
                'contact_phone' => '0700000002'
            ],
            [
                'name' => 'Sunrise Learning Centre',
                'address' => 'Machakos, Kenya',
                'contact_email' => 'support@slc.ac.ke',
                'contact_phone' => '0700000003'
            ],
        ];

        foreach ($schools as $school) {
            School::firstOrCreate(
                ['name' => $school['name']],
                $school
            );
        }
    }
}
