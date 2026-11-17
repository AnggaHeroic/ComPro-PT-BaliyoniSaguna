<?php

// Akun admin awal untuk seeder. Wajib diganti lewat .env sebelum dipakai di server produksi.
return [
    'name' => env('ADMIN_NAME', 'Administrator'),
    'email' => env('ADMIN_EMAIL', 'admin@example.com'),
    'password' => env('ADMIN_PASSWORD'),

    // Salah memasukkan email atau kata sandi sebanyak "max_attempts" kali (dihitung per alamat IP, kedaluwarsa
    // dalam 5 menit) mengunci percobaan berikutnya selama "lockout_seconds" detik.
    'login' => [
        'max_attempts' => 3,
        'lockout_seconds' => 60,
    ],
];
