<?php

namespace Database\Seeders;

use App\Domain\Catalog\Services\ProductCatalogService;
use App\Models\Member;
use App\Models\MemberTier;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductUom;
use App\Models\Supplier;
use App\Models\Uom;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catalogService = app(ProductCatalogService::class);

        // 1. Categories
        $categories = [
            ['code' => 'CAT-BRS', 'name' => 'Beras & Biji-bijian', 'description' => 'Beras premium, medium, ketan, dan aneka palawija.'],
            ['code' => 'CAT-MYK', 'name' => 'Minyak & Lemak', 'description' => 'Minyak goreng curah, kemasan pouch, jerigen, dan mentega.'],
            ['code' => 'CAT-GLA', 'name' => 'Gula & Pemanis', 'description' => 'Gula pasir kristal putih, gula merah, dan sirup.'],
            ['code' => 'CAT-SYR', 'name' => 'Sayur & Bumbu Dapur', 'description' => 'Bawang merah, bawang putih, cabai, dan rempah segar.'],
            ['code' => 'CAT-SSU', 'name' => 'Susu & Olahan', 'description' => 'Susu UHT, kental manis, dan produk olahan susu.'],
            ['code' => 'CAT-KMS', 'name' => 'Kemasan & Plastik', 'description' => 'Kantong plastik, kardus karton, karung goni, dan wadah.'],
        ];

        $catMap = [];
        foreach ($categories as $cat) {
            $catMap[$cat['code']] = ProductCategory::firstOrCreate(['code' => $cat['code']], $cat);
        }

        // 2. Units of Measurement (UOM)
        $uoms = [
            // WEIGHT
            ['code' => 'KG', 'name' => 'Kilogram', 'symbol' => 'kg', 'dimension' => Uom::DIMENSION_WEIGHT, 'precision' => 3],
            ['code' => 'G', 'name' => 'Gram', 'symbol' => 'g', 'dimension' => Uom::DIMENSION_WEIGHT, 'precision' => 0],
            ['code' => 'KRG-25', 'name' => 'Karung 25kg', 'symbol' => 'krg25', 'dimension' => Uom::DIMENSION_WEIGHT, 'precision' => 0],
            ['code' => 'KRG-50', 'name' => 'Karung 50kg', 'symbol' => 'krg50', 'dimension' => Uom::DIMENSION_WEIGHT, 'precision' => 0],

            // COUNT
            ['code' => 'PCS', 'name' => 'Piece / Satuan', 'symbol' => 'pcs', 'dimension' => Uom::DIMENSION_COUNT, 'precision' => 0],
            ['code' => 'LSN', 'name' => 'Lusin (12 Pcs)', 'symbol' => 'lsn', 'dimension' => Uom::DIMENSION_COUNT, 'precision' => 0],
            ['code' => 'KTN-12', 'name' => 'Karton 12 Pcs', 'symbol' => 'ktn12', 'dimension' => Uom::DIMENSION_COUNT, 'precision' => 0],
            ['code' => 'KTN-24', 'name' => 'Karton 24 Pcs', 'symbol' => 'ktn24', 'dimension' => Uom::DIMENSION_COUNT, 'precision' => 0],

            // VOLUME
            ['code' => 'LTR', 'name' => 'Liter', 'symbol' => 'L', 'dimension' => Uom::DIMENSION_VOLUME, 'precision' => 2],
            ['code' => 'ML', 'name' => 'Mililiter', 'symbol' => 'ml', 'dimension' => Uom::DIMENSION_VOLUME, 'precision' => 0],
            ['code' => 'JRG-5', 'name' => 'Jerigen 5 Liter', 'symbol' => 'jrg5L', 'dimension' => Uom::DIMENSION_VOLUME, 'precision' => 0],
            ['code' => 'JRG-20', 'name' => 'Jerigen 20 Liter', 'symbol' => 'jrg20L', 'dimension' => Uom::DIMENSION_VOLUME, 'precision' => 0],
        ];

        $uomMap = [];
        foreach ($uoms as $u) {
            $uomMap[$u['code']] = Uom::firstOrCreate(['code' => $u['code']], $u);
        }

        // 3. Suppliers
        $suppliers = [
            [
                'supplier_code' => 'SUP-001',
                'name' => 'PT Sumber Pangan Nusantara',
                'contact_person' => 'Bpk. Haryono',
                'phone' => '0812-3456-7890',
                'email' => 'sales@sumberpangan.co.id',
                'address' => 'Kawasan Industri Cikarang Blok B4, Bekasi',
            ],
            [
                'supplier_code' => 'SUP-002',
                'name' => 'CV Beras Makmur Sejahtera',
                'contact_person' => 'Ibu Ratna Dewi',
                'phone' => '0813-8899-7711',
                'email' => 'order@berasmakmur.com',
                'address' => 'Jl. Raya Demak - Kudus Km 12, Jawa Tengah',
            ],
            [
                'supplier_code' => 'SUP-003',
                'name' => 'UD Tani Berkah Brebes',
                'contact_person' => 'H. Mansyur',
                'phone' => '0815-4433-2211',
                'email' => 'taniberkahbrebes@gmail.com',
                'address' => 'Sentra Bawang Merah Larangan, Brebes',
            ],
            [
                'supplier_code' => 'SUP-004',
                'name' => 'PT Salim Agro Distribusi',
                'contact_person' => 'Bpk. Albert Wijaya',
                'phone' => '0821-9900-1122',
                'email' => 'albert@salimagro.com',
                'address' => 'Jl. Daan Mogot Km 18, Jakarta Barat',
            ],
        ];

        foreach ($suppliers as $sup) {
            Supplier::firstOrCreate(['supplier_code' => $sup['supplier_code']], $sup);
        }

        // 4. Member Tiers
        $tiers = [
            ['code' => 'REGULAR', 'name' => 'Pelanggan Reguler Ritel', 'priority' => 1, 'discount_percent' => 0.00],
            ['code' => 'SILVER', 'name' => 'Mitra Warung Silver', 'priority' => 2, 'discount_percent' => 2.00],
            ['code' => 'GOLD', 'name' => 'Mitra Grosir Gold', 'priority' => 3, 'discount_percent' => 5.00],
        ];

        $tierMap = [];
        foreach ($tiers as $t) {
            $tierMap[$t['code']] = MemberTier::firstOrCreate(['code' => $t['code']], $t);
        }

        // 5. Members
        $members = [
            [
                'member_code' => 'MBR-B2B-0001',
                'name' => 'Toko Kelontong Bu Siti',
                'type' => Member::TYPE_B2B,
                'tier_id' => $tierMap['GOLD']->id,
                'credit_limit' => 15000000.00,
                'phone' => '0812-9876-5432',
                'email' => 'busiti.kelontong@gmail.com',
                'address' => 'Pasar Induk Blok C No. 12',
            ],
            [
                'member_code' => 'MBR-B2B-0002',
                'name' => 'Warung Sembako Barokah',
                'type' => Member::TYPE_B2B,
                'tier_id' => $tierMap['SILVER']->id,
                'credit_limit' => 7500000.00,
                'phone' => '0813-7766-5544',
                'email' => 'sembakobarokah@yahoo.com',
                'address' => 'Jl. Kebon Jeruk No. 45',
            ],
            [
                'member_code' => 'MBR-RET-0001',
                'name' => 'Bpk. Bambang Sutrisno',
                'type' => Member::TYPE_RETAIL,
                'tier_id' => $tierMap['REGULAR']->id,
                'credit_limit' => 0.00,
                'phone' => '0818-1234-5678',
                'email' => 'bambang.sutrisno@gmail.com',
                'address' => 'Perumahan Indah Asri Blok D3',
            ],
            [
                'member_code' => 'MBR-RET-0002',
                'name' => 'Ibu Hj. Nurhayati',
                'type' => Member::TYPE_RETAIL,
                'tier_id' => $tierMap['REGULAR']->id,
                'credit_limit' => 0.00,
                'phone' => '0819-8765-4321',
                'email' => 'hajah.nurhayati@gmail.com',
                'address' => 'Jl. Merpati Putih No. 8',
            ],
        ];

        foreach ($members as $m) {
            Member::firstOrCreate(['member_code' => $m['member_code']], $m);
        }

        // 6. Products with Multi-UOM & Barcodes
        $products = [
            [
                'sku' => 'SKU-BRS-001',
                'name' => 'Beras Rojolele Super',
                'category_id' => $catMap['CAT-BRS']->id,
                'base_uom_id' => $uomMap['KG']->id,
                'base_barcode' => '8991001001001',
                'description' => 'Beras pulen pilihan kualitas super tanpa pemutih dan wangi alami.',
                'conversions' => [
                    [
                        'uom_id' => $uomMap['KRG-25']->id,
                        'factor_to_base' => 25.000000,
                        'barcode' => '8991001001025',
                        'is_purchase' => true,
                        'is_sale' => true,
                    ],
                    [
                        'uom_id' => $uomMap['KRG-50']->id,
                        'factor_to_base' => 50.000000,
                        'barcode' => '8991001001050',
                        'is_purchase' => true,
                        'is_sale' => true,
                    ],
                ],
            ],
            [
                'sku' => 'SKU-MYK-001',
                'name' => 'Minyak Goreng Bimoli Klasik',
                'category_id' => $catMap['CAT-MYK']->id,
                'base_uom_id' => $uomMap['LTR']->id,
                'base_barcode' => '8992002002001',
                'description' => 'Minyak kelapa sawit murni berkualitas tinggi jernih dan renyah.',
                'conversions' => [
                    [
                        'uom_id' => $uomMap['JRG-5']->id,
                        'factor_to_base' => 5.000000,
                        'barcode' => '8992002002005',
                        'is_purchase' => true,
                        'is_sale' => true,
                    ],
                ],
            ],
            [
                'sku' => 'SKU-GLA-001',
                'name' => 'Gula Pasir Gulaku Putih',
                'category_id' => $catMap['CAT-GLA']->id,
                'base_uom_id' => $uomMap['KG']->id,
                'base_barcode' => '8993003003001',
                'description' => 'Gula pasir kristal putih higienis rasa manis alami tebu.',
                'conversions' => [
                    [
                        'uom_id' => $uomMap['KRG-50']->id,
                        'factor_to_base' => 50.000000,
                        'barcode' => '8993003003050',
                        'is_purchase' => true,
                        'is_sale' => true,
                    ],
                ],
            ],
            [
                'sku' => 'SKU-SYR-001',
                'name' => 'Bawang Merah Brebes Super',
                'category_id' => $catMap['CAT-SYR']->id,
                'base_uom_id' => $uomMap['KG']->id,
                'base_barcode' => '8994004004001',
                'description' => 'Bawang merah kering panen segar dari petani Brebes aroma kuat.',
                'conversions' => [
                    [
                        'uom_id' => $uomMap['KRG-25']->id,
                        'factor_to_base' => 25.000000,
                        'barcode' => '8994004004025',
                        'is_purchase' => true,
                        'is_sale' => false,
                    ],
                ],
            ],
            [
                'sku' => 'SKU-SSU-001',
                'name' => 'Susu UHT Indomilk 1000ml',
                'category_id' => $catMap['CAT-SSU']->id,
                'base_uom_id' => $uomMap['PCS']->id,
                'base_barcode' => '8995005005001',
                'description' => 'Susu sapi steril kaya kalsium dan vitamin D kemasan tetra pak.',
                'conversions' => [
                    [
                        'uom_id' => $uomMap['KTN-12']->id,
                        'factor_to_base' => 12.000000,
                        'barcode' => '8995005005012',
                        'is_purchase' => true,
                        'is_sale' => true,
                    ],
                ],
            ],
        ];

        foreach ($products as $pData) {
            if (!Product::where('sku', $pData['sku'])->exists()) {
                $catalogService->createProduct($pData);
            }
        }
    }
}
