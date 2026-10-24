<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PickupTicket extends Model
{
    use HasFactory;

    public const STATUS_ISSUED = 'ISSUED';
    public const STATUS_REDEEMED = 'REDEEMED';
    public const STATUS_EXPIRED = 'EXPIRED';
    public const STATUS_REVOKED = 'REVOKED';

    protected $fillable = [
        'b2b_order_id',
        'ticket_number',
        'token_hash',
        'raw_token_preview',
        'fulfillment_location_id',
        'status',
        'issued_at',
        'expires_at',
        'redeemed_at',
        'redeemed_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'expires_at' => 'datetime',
            'redeemed_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(B2bOrder::class, 'b2b_order_id');
    }

    public function fulfillmentLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'fulfillment_location_id');
    }

    public function redeemedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redeemed_by_user_id');
    }

    public function isRedeemable(): bool
    {
        return $this->status === self::STATUS_ISSUED && (!$this->expires_at || $this->expires_at->isFuture());
    }

    /**
     * Generate an SVG representation of the Pickup QR token
     * adhering to flat design standards (no external dependencies required).
     */
    public function generateQrSvg(int $size = 200): string
    {
        $hash = $this->token_hash ?? hash('sha256', $this->ticket_number);
        $gridSize = 19; // 19x19 QR matrix grid
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
            $rects .= "<rect x='{$cx}' y='{$cy}' width='{$cw}' height='{$cw}' rx='1.5' fill='#8B5CF6'/>";
        };

        $finder(0, 0);
        $finder(12, 0);
        $finder(0, 12);

        // Data dots based on token_hash
        for ($row = 0; $row < $gridSize; $row++) {
            for ($col = 0; $col < $gridSize; $col++) {
                // Skip finder pattern zones
                if (($row < 8 && $col < 8) || ($row < 8 && $col >= 11) || ($row >= 11 && $col < 8)) {
                    continue;
                }
                $bitIndex = ($row * $gridSize + $col) % 64;
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
<svg width="{$size}" height="{$size}" viewBox="0 0 {$size} {$size}" fill="none" xmlns="http://www.w3.org/2000/svg" class="mx-auto rounded-2xl bg-white p-3 border-2 border-[#8B5CF6]/30 shadow-md">
    {$rects}
</svg>
SVG;
    }
}
