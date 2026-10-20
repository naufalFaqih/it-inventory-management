<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $headquarters = Location::firstOrCreate(
            ['name' => 'Kantor Pusat'],
            ['description' => 'Gedung Utama Kantor Pusat']
        );

        $branch = Location::firstOrCreate(
            ['name' => 'Kantor Cabang'],
            ['description' => 'Gedung Operasional Cabang']
        );

        $rooms = [
            ['name' => 'Ruang Server Lantai 1', 'parent_id' => $headquarters->id, 'description' => 'Data center & rack server'],
            ['name' => 'Ruang IT Support Lantai 2', 'parent_id' => $headquarters->id, 'description' => 'Ruang kerja tim IT Support & Helpdesk'],
            ['name' => 'Gudang Penyimpanan IT Lantai 1', 'parent_id' => $headquarters->id, 'description' => 'Tempat penyimpanan spare part dan stok aset'],
            ['name' => 'Ruang Operasional Lantai 3', 'parent_id' => $headquarters->id, 'description' => 'Ruang kerja umum staff operasional'],
            ['name' => 'Ruang Server Cabang', 'parent_id' => $branch->id, 'description' => 'Server rack cabang'],
        ];

        foreach ($rooms as $room) {
            Location::firstOrCreate(
                ['name' => $room['name'], 'parent_id' => $room['parent_id']],
                $room
            );
        }
    }
}
