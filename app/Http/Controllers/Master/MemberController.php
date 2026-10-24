<?php

namespace App\Http\Controllers\Master;

use App\Domain\Membership\Services\MemberService;
use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberTier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function __construct(
        protected MemberService $memberService
    ) {}

    public function index(Request $request): View
    {
        $query = Member::with('tier');

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('member_code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $perPage = min(max((int) $request->input('per_page', 15), 5), 100);
        $members = $query->orderBy('name')->paginate($perPage)->withQueryString();
        $tiers = MemberTier::active()->orderBy('priority')->get();

        return view('master.members.index', compact('members', 'tiers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_code' => ['nullable', 'string', 'max:60', 'unique:members,member_code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:RETAIL,B2B'],
            'tier_id' => ['nullable', 'exists:member_tiers,id'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'phone' => ['nullable', 'string', 'max:35'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $member = $this->memberService->createMember($validated);

        return back()->with('success', "Member [{$member->name}] ({$member->member_code}) berhasil didaftarkan!");
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:RETAIL,B2B'],
            'tier_id' => ['nullable', 'exists:member_tiers,id'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'phone' => ['nullable', 'string', 'max:35'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $this->memberService->updateMember($member, $validated);

        return back()->with('success', "Data member [{$member->name}] berhasil diperbarui!");
    }

    public function destroy(Member $member): RedirectResponse
    {
        $this->memberService->deactivateMember($member);
        return back()->with('success', "Member [{$member->name}] berhasil dinonaktifkan.");
    }

    /**
     * Show Digital Member Card with QR Vector Code
     */
    public function card(Member $member): View
    {
        $member->load('tier');
        $qrSvg = $member->generateQrSvg(180);

        return view('master.members.card', compact('member', 'qrSvg'));
    }
}
