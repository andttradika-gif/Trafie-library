<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrNew(['email' => 'admin@trafie.com']);
        if (!$admin->exists) {
            $admin->password = Hash::make('admin12345');
        }
        $admin->name = 'Admin Trafie';
        $admin->role = 'admin';
        $admin->email_verified_at = now();
        $admin->save();

        foreach (['Novel', 'Fiksi', 'Non-Fiksi', 'Sains', 'Sejarah', 'Pendidikan'] as $nama) {
            Category::firstOrCreate(['slug' => Str::slug($nama)], ['name' => $nama]);
        }
    }
}