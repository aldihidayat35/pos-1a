<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        $whLocation = Location::where('code', 'WH-CENTRAL')->first();
        $storeLocation = Location::where('code', 'STR-PASAR-1')->first();

        $users = [
            [
                'name' => 'Super Administrator',
                'username' => 'superadmin',
                'email' => 'admin@pasarhub.id',
                'phone' => '08110000001',
                'role' => 'super_admin',
                'locations' => [$whLocation?->id, $storeLocation?->id],
                'default_loc' => $whLocation?->id,
            ],
            [
                'name' => 'Barbara W.',
                'username' => 'owner',
                'email' => 'owner@pasarhub.id',
                'phone' => '08110000002',
                'role' => 'owner',
                'locations' => [$whLocation?->id, $storeLocation?->id],
                'default_loc' => $storeLocation?->id,
            ],
            [
                'name' => 'Raka Pratama',
                'username' => 'kepalatoko',
                'email' => 'kepala.toko@pasarhub.id',
                'phone' => '08110000003',
                'role' => 'kepala_toko',
                'locations' => [$storeLocation?->id],
                'default_loc' => $storeLocation?->id,
            ],
            [
                'name' => 'Budi Santoso',
                'username' => 'kepalagudang',
                'email' => 'kepala.gudang@pasarhub.id',
                'phone' => '08110000004',
                'role' => 'kepala_gudang',
                'locations' => [$whLocation?->id],
                'default_loc' => $whLocation?->id,
            ],
            [
                'name' => 'Hendra Setiawan',
                'username' => 'stafgudang',
                'email' => 'staf.gudang@pasarhub.id',
                'phone' => '08110000005',
                'role' => 'staf_gudang',
                'locations' => [$whLocation?->id],
                'default_loc' => $whLocation?->id,
            ],
            [
                'name' => 'Maya Kartika',
                'username' => 'staftoko',
                'email' => 'staf.toko@pasarhub.id',
                'phone' => '08110000006',
                'role' => 'staf_toko',
                'locations' => [$storeLocation?->id],
                'default_loc' => $storeLocation?->id,
            ],
            [
                'name' => 'Doni Wibowo',
                'username' => 'sales',
                'email' => 'sales@pasarhub.id',
                'phone' => '08110000007',
                'role' => 'sales',
                'locations' => [$whLocation?->id, $storeLocation?->id],
                'default_loc' => $whLocation?->id,
            ],
            [
                'name' => 'CV Berkah Jaya (Mitra)',
                'username' => 'mitrab2b',
                'email' => 'mitra.b2b@pasarhub.id',
                'phone' => '08110000008',
                'role' => 'b2b_mitra',
                'locations' => [$storeLocation?->id],
                'default_loc' => $storeLocation?->id,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['username' => $userData['username']],
                [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'phone' => $userData['phone'],
                    'preferred_locale' => 'id',
                    'password' => $password,
                    'is_active' => true,
                ]
            );

            // Assign role
            $role = Role::where('name', $userData['role'])->first();
            if ($role) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            }

            // Assign locations
            $locIds = array_filter($userData['locations']);
            foreach ($locIds as $locId) {
                $isDefault = ($locId === $userData['default_loc']);
                $user->locations()->syncWithoutDetaching([
                    $locId => ['is_default' => $isDefault]
                ]);
            }
        }
    }
}
