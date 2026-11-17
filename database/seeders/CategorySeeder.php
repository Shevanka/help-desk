<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public const CATEGORIES = [
        'Hardware' => 'Kerusakan perangkat keras: PC, laptop, printer, proyektor.',
        'Software' => 'Instalasi, lisensi, atau error pada aplikasi.',
        'Network' => 'Gangguan koneksi internet, Wi-Fi, atau jaringan lokal.',
        'Account & Access' => 'Masalah login, reset password, hak akses sistem informasi.',
        'Laboratory Equipment' => 'Perangkat dan fasilitas laboratorium komputer.',
        'Other' => 'Permasalahan lain yang tidak termasuk kategori di atas.',
    ];

    public function run(): void
    {
        foreach (self::CATEGORIES as $name => $description) {
            Category::updateOrCreate(['name' => $name], ['description' => $description, 'is_active' => true]);
        }
    }
}
