<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminService
{
    public function registerAdmin(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'county' => $data['county'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'role' => 'admin',
                'status' => 'active',
            ]);

            $admin = Admin::create([
                'user_id' => $user->id,
                'admin_level' => $data['admin_level'],
                'school_id' => $data['school_id'] ?? null,
            ]);

            return $user->load('admin');
        });
    }
}
