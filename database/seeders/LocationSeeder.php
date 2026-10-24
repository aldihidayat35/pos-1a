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
        $locations = [
            [
                'code' => 'WH-CENTRAL',
                'name' => 'Gudang Pusat Sentral',
                'type' => Location::TYPE_WAREHOUSE,
                'phone' => '021-5550101',
                'address' => 'Kawasan Industri Pasar Induk Blok C-05',
                'is_active' => true,
            ],
            [
                'code' => 'STR-PASAR-1',
                'name' => 'Toko Pasar Raya Blok A',
                'type' => Location::TYPE_STORE,
                'phone' => '021-5550202',
                'address' => 'Pasar Raya Sentral, Lantai Dasar No. 12-14',
                'is_active' => true,
            ],
            [
                'code' => 'LOC-TRANSIT',
                'name' => 'Transit Antar Lokasi (Virtual)',
                'type' => Location::TYPE_TRANSIT,
                'phone' => null,
                'address' => 'Lokasi virtual untuk barang dalam perjalanan distribusi',
                'is_active' => true,
            ],
        ];

        foreach ($locations as $loc) {
            Location::firstOrCreate(['code' => $loc['code']], $loc);
        }
    }
}
