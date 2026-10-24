<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\MemberTier;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MasterMemberTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected MemberTier $goldTier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::where('username', 'superadmin')->first();

        $this->goldTier = MemberTier::firstOrCreate(
            ['code' => 'GOLD'],
            ['name' => 'Mitra Grosir Gold', 'priority' => 3, 'discount_percent' => 5.0]
        );
    }

    public function test_user_can_view_member_list(): void
    {
        $response = $this->actingAs($this->user)->get(route('master.members.index'));

        $response->assertStatus(200);
        $response->assertSee('Master Pelanggan, Member & Kemitraan B2B');
    }

    public function test_user_can_create_member_with_auto_generated_code(): void
    {
        $payload = [
            'name' => 'Toko Kelontong Berkah Jaya',
            'type' => Member::TYPE_B2B,
            'tier_id' => $this->goldTier->id,
            'credit_limit' => 20000000.0,
            'phone' => '0812-9988-7766',
            'email' => 'berkahjaya@gmail.com',
            'address' => 'Pasar Induk Los B-24',
        ];

        $response = $this->actingAs($this->user)->post(route('master.members.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('members', [
            'name' => 'Toko Kelontong Berkah Jaya',
            'type' => Member::TYPE_B2B,
            'credit_limit' => 20000000.0,
        ]);

        $member = Member::where('name', 'Toko Kelontong Berkah Jaya')->first();
        $this->assertNotNull($member);
        $this->assertStringStartsWith('MBR-B2B-', $member->member_code);
    }

    public function test_member_digital_card_renders_qr_code(): void
    {
        $member = Member::create([
            'member_code' => 'MBR-QR-TEST-01',
            'name' => 'Bpk. Hendrawan',
            'type' => Member::TYPE_RETAIL,
            'tier_id' => $this->goldTier->id,
            'credit_limit' => 0.0,
        ]);

        $response = $this->actingAs($this->user)->get(route('master.members.card', $member));

        $response->assertStatus(200);
        $response->assertSee('MBR-QR-TEST-01');
        $response->assertSee('Bpk. Hendrawan');
        $response->assertSee('<svg', false); // Contains SVG QR code
    }

    public function test_member_can_be_soft_deactivated(): void
    {
        $member = Member::create([
            'member_code' => 'MBR-DEACT-01',
            'name' => 'Member Nonaktif',
            'type' => Member::TYPE_RETAIL,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->delete(route('master.members.destroy', $member));

        $response->assertRedirect();
        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'is_active' => false,
        ]);
    }
}
