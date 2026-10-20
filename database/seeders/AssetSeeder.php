<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@inventory.local')->first();
        $staff = User::where('email', 'staff@inventory.local')->first();
        $viewer = User::where('email', 'viewer@inventory.local')->first();

        $laptopCat = Category::where('name', 'Laptop')->first();
        $serverCat = Category::where('name', 'Server')->first();
        $switchCat = Category::where('name', 'Switch & Router')->first();
        $monitorCat = Category::where('name', 'Monitor')->first();

        $serverRoom = Location::where('name', 'Ruang Server Lantai 1')->first();
        $supportRoom = Location::where('name', 'Ruang IT Support Lantai 2')->first();
        $warehouse = Location::where('name', 'Gudang Penyimpanan IT Lantai 1')->first();
        $officeRoom = Location::where('name', 'Ruang Operasional Lantai 3')->first();

        $initialAssets = [
            [
                'asset_tag' => 'AST-2026-0001',
                'name' => 'ThinkPad T14 Gen 4',
                'category_id' => $laptopCat->id,
                'brand' => 'Lenovo',
                'model' => 'ThinkPad T14 AMD Ryzen 7',
                'serial_number' => 'PF-4X9901A',
                'location_id' => $officeRoom->id,
                'assigned_to' => $viewer->id,
                'condition' => Asset::CONDITION_GOOD,
                'status' => Asset::STATUS_ASSIGNED,
                'purchase_date' => '2026-01-15',
                'purchase_cost' => 18500000.00,
                'notes' => 'Laptop operasional staf finance',
                'created_by' => $admin->id,
            ],
            [
                'asset_tag' => 'AST-2026-0002',
                'name' => 'Dell PowerEdge R750',
                'category_id' => $serverCat->id,
                'brand' => 'Dell',
                'model' => 'PowerEdge R750xs 2U',
                'serial_number' => 'DL-9938210-SRV',
                'location_id' => $serverRoom->id,
                'assigned_to' => null,
                'condition' => Asset::CONDITION_GOOD,
                'status' => Asset::STATUS_AVAILABLE,
                'purchase_date' => '2025-11-20',
                'purchase_cost' => 85000000.00,
                'notes' => 'Server virtualisasi node 1',
                'created_by' => $admin->id,
            ],
            [
                'asset_tag' => 'AST-2026-0003',
                'name' => 'Cisco Catalyst 9300 48-Port',
                'category_id' => $switchCat->id,
                'brand' => 'Cisco',
                'model' => 'C9300-48P-A',
                'serial_number' => 'FCW2348L01B',
                'location_id' => $serverRoom->id,
                'assigned_to' => null,
                'condition' => Asset::CONDITION_GOOD,
                'status' => Asset::STATUS_AVAILABLE,
                'purchase_date' => '2025-08-10',
                'purchase_cost' => 45000000.00,
                'notes' => 'Core switch lantai 1',
                'created_by' => $admin->id,
            ],
            [
                'asset_tag' => 'AST-2026-0004',
                'name' => 'MacBook Pro 14 M3 Pro',
                'category_id' => $laptopCat->id,
                'brand' => 'Apple',
                'model' => 'MacBook Pro 14" M3 Pro 18GB/512GB',
                'serial_number' => 'C02G9901Q6L4',
                'location_id' => $supportRoom->id,
                'assigned_to' => $staff->id,
                'condition' => Asset::CONDITION_GOOD,
                'status' => Asset::STATUS_ASSIGNED,
                'purchase_date' => '2026-02-01',
                'purchase_cost' => 31999000.00,
                'notes' => 'Laptop development & support lead',
                'created_by' => $admin->id,
            ],
            [
                'asset_tag' => 'AST-2026-0005',
                'name' => 'Dell UltraSharp 27 4K U2723QE',
                'category_id' => $monitorCat->id,
                'brand' => 'Dell',
                'model' => 'U2723QE IPS Black',
                'serial_number' => 'CN-093842-DL',
                'location_id' => $warehouse->id,
                'assigned_to' => null,
                'condition' => Asset::CONDITION_GOOD,
                'status' => Asset::STATUS_AVAILABLE,
                'purchase_date' => '2026-03-05',
                'purchase_cost' => 8200000.00,
                'notes' => 'Stok cadangan monitor di gudang IT',
                'created_by' => $staff->id,
            ],
            [
                'asset_tag' => 'AST-2026-0006',
                'name' => 'HP EliteBook 840 G9',
                'category_id' => $laptopCat->id,
                'brand' => 'HP',
                'model' => 'EliteBook 840 G9 Intel i7',
                'serial_number' => '5CD2348821',
                'location_id' => $supportRoom->id,
                'assigned_to' => null,
                'condition' => Asset::CONDITION_MINOR_DAMAGE,
                'status' => Asset::STATUS_MAINTENANCE,
                'purchase_date' => '2024-05-12',
                'purchase_cost' => 19500000.00,
                'notes' => 'Keyboard tidak responsif beberapa tombol, sedang diservis',
                'created_by' => $staff->id,
            ],
        ];

        foreach ($initialAssets as $assetData) {
            Asset::firstOrCreate(
                ['asset_tag' => $assetData['asset_tag']],
                $assetData
            );
        }
    }
}
