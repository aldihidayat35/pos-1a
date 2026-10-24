<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    public const TYPE_RETAIL = 'RETAIL';
    public const TYPE_B2B = 'B2B';

    protected $fillable = [
        'member_code',
        'name',
        'type',
        'tier_id',
        'credit_limit',
        'phone',
        'email',
        'address',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'credit_limit' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function tier(): BelongsTo
    {
        return $this->belongsTo(MemberTier::class, 'tier_id');
    }

    public function memberUsers(): HasMany
    {
        return $this->hasMany(MemberUser::class, 'member_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'member_users', 'member_id', 'user_id')
            ->withPivot('contact_role')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRetail($query)
    {
        return $query->where('type', self::TYPE_RETAIL);
    }

    public function scopeB2b($query)
    {
        return $query->where('type', self::TYPE_B2B);
    }

    /**
     * Generate an SVG representation of the Member Code (QR-like / Barcode vector)
     * adhering to flat design standards (no external dependencies required).
     */
    public function generateQrSvg(int $size = 180): string
    {
        $hash = md5($this->member_code);
        $gridSize = 17; // 17x17 QR-like matrix grid
        $cellSize = floor($size / $gridSize);
        $offset = ($size - ($cellSize * $gridSize)) / 2;

        $rects = '';
        // 3 Corner Position Detection Patterns (Finder Patterns)
        $finder = function ($startX, $startY) use ($cellSize, &$rects, $offset) {
            // Outer 7x7
            $x = $offset + ($startX * $cellSize);
            $y = $offset + ($startY * $cellSize);
            $w = 7 * $cellSize;
            $rects .= "<rect x='{$x}' y='{$y}' width='{$w}' height='{$w}' rx='4' fill='#342A6E'/>";
            // Inner white 5x5
            $ix = $x + $cellSize;
            $iy = $y + $cellSize;
            $iw = 5 * $cellSize;
            $rects .= "<rect x='{$ix}' y='{$iy}' width='{$iw}' height='{$iw}' rx='2' fill='#FFFFFF'/>";
            // Center 3x3
            $cx = $x + (2 * $cellSize);
            $cy = $y + (2 * $cellSize);
            $cw = 3 * $cellSize;
            $rects .= "<rect x='{$cx}' y='{$cy}' width='{$cw}' height='{$cw}' rx='1.5' fill='#342A6E'/>";
        };

        $finder(0, 0);
        $finder(10, 0);
        $finder(0, 10);

        // Data dots based on member_code hash
        for ($row = 0; $row < $gridSize; $row++) {
            for ($col = 0; $col < $gridSize; $col++) {
                // Skip finder pattern zones
                if (($row < 8 && $col < 8) || ($row < 8 && $col >= 9) || ($row >= 9 && $col < 8)) {
                    continue;
                }
                $bitIndex = ($row * $gridSize + $col) % 32;
                $bitVal = hexdec($hash[$bitIndex]);
                if ($bitVal % 2 === 1) {
                    $x = $offset + ($col * $cellSize);
                    $y = $offset + ($row * $cellSize);
                    $s = $cellSize - 1;
                    $rects .= "<rect x='{$x}' y='{$y}' width='{$s}' height='{$s}' rx='1' fill='#342A6E'/>";
                }
            }
        }

        return <<<SVG
<svg width="{$size}" height="{$size}" viewBox="0 0 {$size} {$size}" fill="none" xmlns="http://www.w3.org/2000/svg" class="mx-auto rounded-xl bg-white p-2 border border-[#ECEBF4] shadow-sm">
    {$rects}
</svg>
SVG;
    }
}
