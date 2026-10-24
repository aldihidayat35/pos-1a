<?php

namespace App\Domain\Membership\Services;

use App\Models\Member;
use Illuminate\Support\Str;

class MemberService
{
    /**
     * Generate unique member code (e.g. MBR-RET-0001, MBR-B2B-0001)
     */
    public function generateMemberCode(string $type): string
    {
        $prefix = ($type === Member::TYPE_B2B) ? 'MBR-B2B' : 'MBR-RET';
        $count = Member::where('type', $type)->count() + 1;
        $code = sprintf('%s-%04d', $prefix, $count);

        while (Member::where('member_code', $code)->exists()) {
            $count++;
            $code = sprintf('%s-%04d', $prefix, $count);
        }

        return $code;
    }

    /**
     * Create member
     */
    public function createMember(array $data): Member
    {
        $type = $data['type'] ?? Member::TYPE_RETAIL;
        $code = !empty($data['member_code']) ? trim($data['member_code']) : $this->generateMemberCode($type);

        return Member::create([
            'member_code' => strtoupper($code),
            'name' => trim($data['name']),
            'type' => $type,
            'tier_id' => $data['tier_id'] ?? null,
            'credit_limit' => max(0, (float) ($data['credit_limit'] ?? 0)),
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Update member
     */
    public function updateMember(Member $member, array $data): Member
    {
        $member->update([
            'name' => trim($data['name'] ?? $member->name),
            'type' => $data['type'] ?? $member->type,
            'tier_id' => $data['tier_id'] ?? $member->tier_id,
            'credit_limit' => isset($data['credit_limit']) ? max(0, (float) $data['credit_limit']) : $member->credit_limit,
            'phone' => $data['phone'] ?? $member->phone,
            'email' => $data['email'] ?? $member->email,
            'address' => $data['address'] ?? $member->address,
            'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : $member->is_active,
        ]);

        return $member->fresh(['tier']);
    }

    /**
     * Soft deactivation for Member
     */
    public function deactivateMember(Member $member): bool
    {
        return $member->update(['is_active' => false]);
    }
}
