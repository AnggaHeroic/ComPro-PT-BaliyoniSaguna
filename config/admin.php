<?php

// Akun admin awal untuk seeder. Wajib diganti lewat .env sebelum dipakai di server produksi.
return [
    'name' => env('ADMIN_NAME', 'Administrator'),
    'email' => env('ADMIN_EMAIL', 'admin@example.com'),
    'password' => env('ADMIN_PASSWORD'),
];
