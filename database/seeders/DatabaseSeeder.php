<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = config('admin.password');

        // Tanpa kata sandi di .env, akun tidak dibuat agar tidak ada akun dengan sandi bawaan.
        if (! $password) {
            $this->command?->warn('ADMIN_PASSWORD belum diisi di .env, akun admin tidak dibuat.');

            return;
        }

        User::updateOrCreate(
            ['email' => config('admin.email')],
            ['name' => config('admin.name'), 'password' => $password],
        );
    }
}
