<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductUom;
use App\Models\Uom;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MasterProductTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected ProductCategory $category;
    protected Uom $baseUomKg;
    protected Uom $convUomKrg25;
    protected Uom $convUomLiter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::where('username', 'superadmin')->first();

        $this->category = ProductCategory::firstOrCreate(
            ['code' => 'CAT-TEST'],
            ['name' => 'Kategori Uji Coba']
        );

        $this->baseUomKg = Uom::firstOrCreate(
            ['code' => 'KG'],
            ['name' => 'Kilogram', 'symbol' => 'kg', 'dimension' => Uom::DIMENSION_WEIGHT, 'precision' => 3]
        );

        $this->convUomKrg25 = Uom::firstOrCreate(
            ['code' => 'KRG-25'],
            ['name' => 'Karung 25kg', 'symbol' => 'krg25', 'dimension' => Uom::DIMENSION_WEIGHT, 'precision' => 0]
        );

        $this->convUomLiter = Uom::firstOrCreate(
            ['code' => 'LTR'],
            ['name' => 'Liter', 'symbol' => 'L', 'dimension' => Uom::DIMENSION_VOLUME, 'precision' => 2]
        );
    }

    public function test_user_can_view_product_catalog_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('master.products.index'));

        $response->assertStatus(200);
        $response->assertSee('Katalog Master Produk');
    }

    public function test_user_can_create_product_with_base_and_conversion_uoms(): void
    {
        $payload = [
            'sku' => 'SKU-TEST-001',
            'name' => 'Beras Pandan Wangi Cianjur',
            'category_id' => $this->category->id,
            'base_uom_id' => $this->baseUomKg->id,
            'base_barcode' => '8999999001001',
            'description' => 'Beras pandan wangi asli Cianjur aroma pandan alami.',
            'conversions' => [
                [
                    'uom_id' => $this->convUomKrg25->id,
                    'factor_to_base' => 25.0,
                    'barcode' => '8999999001025',
                    'is_purchase' => 1,
                    'is_sale' => 1,
                ]
            ],
        ];

        $response = $this->actingAs($this->user)->post(route('master.products.store'), $payload);

        $response->assertRedirect(route('master.products.index'));
        $this->assertDatabaseHas('products', [
            'sku' => 'SKU-TEST-001',
            'name' => 'Beras Pandan Wangi Cianjur',
        ]);

        $product = Product::where('sku', 'SKU-TEST-001')->first();
        $this->assertNotNull($product);

        // Check base ProductUom
        $this->assertDatabaseHas('product_uoms', [
            'product_id' => $product->id,
            'uom_id' => $this->baseUomKg->id,
            'factor_to_base' => 1.0,
            'barcode' => '8999999001001',
            'is_base' => true,
        ]);

        // Check conversion ProductUom
        $this->assertDatabaseHas('product_uoms', [
            'product_id' => $product->id,
            'uom_id' => $this->convUomKrg25->id,
            'factor_to_base' => 25.0,
            'barcode' => '8999999001025',
            'is_base' => false,
        ]);
    }

    public function test_sku_must_be_unique(): void
    {
        // First product
        Product::create([
            'sku' => 'SKU-DUPLICATE-1',
            'name' => 'Gula Pasir Kristal',
            'normalized_name' => 'gula pasir kristal',
            'category_id' => $this->category->id,
            'base_uom_id' => $this->baseUomKg->id,
        ]);

        // Second product with identical SKU
        $payload = [
            'sku' => 'SKU-DUPLICATE-1',
            'name' => 'Gula Pasir Lain',
            'category_id' => $this->category->id,
            'base_uom_id' => $this->baseUomKg->id,
        ];

        $response = $this->actingAs($this->user)->post(route('master.products.store'), $payload);

        $response->assertSessionHasErrors('sku');
    }

    public function test_barcode_must_be_unique_across_product_uoms(): void
    {
        $existingProduct = Product::create([
            'sku' => 'SKU-BARCODE-1',
            'name' => 'Minyak Goreng A',
            'normalized_name' => 'minyak goreng a',
            'category_id' => $this->category->id,
            'base_uom_id' => $this->baseUomKg->id,
        ]);

        ProductUom::create([
            'product_id' => $existingProduct->id,
            'uom_id' => $this->baseUomKg->id,
            'factor_to_base' => 1.0,
            'barcode' => '8998888001001',
            'is_base' => true,
        ]);

        // Try registering new product with same barcode
        $payload = [
            'sku' => 'SKU-BARCODE-2',
            'name' => 'Minyak Goreng B',
            'category_id' => $this->category->id,
            'base_uom_id' => $this->baseUomKg->id,
            'base_barcode' => '8998888001001', // duplicate barcode
        ];

        $response = $this->actingAs($this->user)->post(route('master.products.store'), $payload);

        $response->assertSessionHasErrors('base_barcode');
    }

    public function test_cross_dimension_uom_conversion_is_rejected(): void
    {
        // Try creating Weight product with Volume conversion (Kg -> Liter)
        $payload = [
            'sku' => 'SKU-DIM-FAIL',
            'name' => 'Beras Campur Air',
            'category_id' => $this->category->id,
            'base_uom_id' => $this->baseUomKg->id, // WEIGHT
            'conversions' => [
                [
                    'uom_id' => $this->convUomLiter->id, // VOLUME (Cross-dimension!)
                    'factor_to_base' => 1.0,
                ]
            ],
        ];

        $response = $this->actingAs($this->user)->post(route('master.products.store'), $payload);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('products', ['sku' => 'SKU-DIM-FAIL']);
    }

    public function test_fuzzy_similarity_check_warns_on_similar_product_names(): void
    {
        Product::create([
            'sku' => 'SKU-FUZZY-1',
            'name' => 'Beras Rojolele Super',
            'normalized_name' => Product::normalizeName('Beras Rojolele Super'),
            'category_id' => $this->category->id,
            'base_uom_id' => $this->baseUomKg->id,
        ]);

        // Query API with slightly different phrasing
        $response = $this->actingAs($this->user)->getJson(
            route('master.products.check_duplicate', ['name' => 'Beras Rojolele Super 50kg'])
        );

        $response->assertStatus(200);
        $response->assertJsonPath('has_warning', true);
        $this->assertGreaterThanOrEqual(1, $response->json('count'));
        $this->assertGreaterThanOrEqual(75.0, $response->json('duplicates.0.similarity'));
    }

    public function test_product_can_be_soft_deactivated(): void
    {
        $product = Product::create([
            'sku' => 'SKU-DEACTIVATE-1',
            'name' => 'Barang Mau Dihapus',
            'normalized_name' => 'barang mau dihapus',
            'category_id' => $this->category->id,
            'base_uom_id' => $this->baseUomKg->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->delete(route('master.products.destroy', $product));

        $response->assertRedirect(route('master.products.index'));
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'is_active' => false,
        ]);
    }
}
