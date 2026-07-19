<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('admin.email');
        $password = config('admin.password');

        if (empty($password)) {
            $this->command->warn('Bỏ qua AdminUserSeeder: chưa cấu hình ADMIN_PASSWORD trong .env');

            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => config('admin.name'),
                'password' => Hash::make($password),
                'is_admin' => true,
            ],
        );
    }
}
