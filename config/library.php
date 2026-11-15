<?php

return [
    // Denda per hari keterlambatan (rupiah). Bisa diubah lewat .env: LIBRARY_FINE_PER_DAY=2000
    'fine_per_day' => (int) env('LIBRARY_FINE_PER_DAY', 1000),

    // Kontak dan sosial media di footer landing. Ubah lewat .env.
    'contact' => [
        'email'   => env('LIBRARY_EMAIL', 'trafie-library@school.id'),
        'phone'   => env('LIBRARY_PHONE', '+62 812 3456 7890'),
        'address' => env('LIBRARY_ADDRESS', 'SMK Negeri 1 Ciomas, Bogor'),
    ],
    'social' => [
        'instagram' => env('LIBRARY_INSTAGRAM', '#'),
        'tiktok'    => env('LIBRARY_TIKTOK', '#'),
        'youtube'   => env('LIBRARY_YOUTUBE', '#'),
    ],
];