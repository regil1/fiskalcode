<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user pertama yang terdaftar (biasanya ID 1)
        $user = User::first();
        
        if (!$user) {
            $this->command->error("Belum ada user terdaftar. Silakan Register dulu di website!");
            return;
        }

        // Masukkan data kategori contoh
        DB::table('categories')->insert([
            [
                'user_id' => $user->id,
                'name' => 'Makanan & Minuman',
                'type' => 'expense',
                'budget_limit' => 1500000, // Limit 1.5 Juta
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'name' => 'Transportasi',
                'type' => 'expense',
                'budget_limit' => 500000, // Limit 500 Ribu
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'name' => 'Gaji Bulanan',
                'type' => 'income',
                'budget_limit' => null, // Pemasukan tidak butuh limit
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        
        $this->command->info("✅ Kategori berhasil diisi untuk user: " . $user->email);
    }
}
