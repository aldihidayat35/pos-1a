<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\User;
use Tests\TestCase;

class LocationScopeTest extends TestCase
{
    public function test_user_gets_active_location_assigned_on_dashboard_access(): void
    {
        $kepalaToko = User::where('username', 'kepalatoko')->first();
        $this->assertNotNull($kepalaToko);

        $response = $this->actingAs($kepalaToko)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Toko Pasar Raya Blok A');
    }

    public function test_multi_location_user_can_switch_active_location(): void
    {
        $superAdmin = User::where('username', 'superadmin')->first();
        $this->assertNotNull($superAdmin);

        $whLocation = Location::where('code', 'WH-CENTRAL')->first();
        $storeLocation = Location::where('code', 'STR-PASAR-1')->first();

        // Switch to warehouse
        $response1 = $this->actingAs($superAdmin)->post('/location/switch', [
            'location_id' => $whLocation->id,
        ]);
        $response1->assertSessionHas('active_location_id', $whLocation->id);

        // Switch to store
        $response2 = $this->actingAs($superAdmin)->post('/location/switch', [
            'location_id' => $storeLocation->id,
        ]);
        $response2->assertSessionHas('active_location_id', $storeLocation->id);
    }

    public function test_user_cannot_switch_to_unauthorized_location(): void
    {
        $stafToko = User::where('username', 'staftoko')->first();
        $this->assertNotNull($stafToko);

        // WH-CENTRAL is not assigned to Staf Toko (Maya)
        $whLocation = Location::where('code', 'WH-CENTRAL')->first();
        $this->assertFalse($stafToko->locations->contains('id', $whLocation->id));

        $response = $this->actingAs($stafToko)
            ->from('/dashboard')
            ->post('/location/switch', [
                'location_id' => $whLocation->id,
            ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
    }
}
