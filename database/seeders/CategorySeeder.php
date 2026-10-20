<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Laptop', 'description' => 'Perangkat laptop kerja operasional'],
            ['name' => 'Desktop PC', 'description' => 'Workstation dan komputer desktop'],
            ['name' => 'Monitor', 'description' => 'Layar monitor eksternal berbagai ukuran'],
            ['name' => 'Server', 'description' => 'Server fisik rackmount dan tower'],
            ['name' => 'Switch & Router', 'description' => 'Perangkat jaringan switch manageable dan router'],
            ['name' => 'Access Point', 'description' => 'Perangkat wireless AP kantor'],
            ['name' => 'Printer & Scanner', 'description' => 'Perangkat cetak dan pemindai dokumen'],
            ['name' => 'IP Phone', 'description' => 'Perangkat telepon VoIP internal'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
