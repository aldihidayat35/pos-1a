<?php

namespace App\Services;

use App\Models\InventoryBalance;
use App\Models\Location;
use App\Models\MemberTier;
use App\Models\PriceRule;
use App\Models\PricingPolicy;
use App\Models\Product;

class PricingService
{
    /**
     * Calculate HPP, Markup, Floor Price, and Market Boundaries
     * based on PRD Section 4.3 & Blueprint Section 5.2
     */
    public function calculatePricingLimits(
        Product $product,
        Location $location,
        string $channel = 'RETAIL',
        ?MemberTier $tier = null
    ): array {
        // 1. Current Moving Average HPP at specific location
        $balance = InventoryBalance::where('location_id', $location->id)
            ->where('product_id', $product->id)
            ->first();
        $currentHpp = (float) ($balance?->avg_unit_cost ?? 0.0);
        if ($currentHpp <= 0.0) {
            $currentHpp = (float) ($product->last_purchase_price ?? 0.0);
        }

        // 2. Applicable Pricing Policy (Location scope prioritized, then Global)
        $policy = PricingPolicy::where('is_active', true)
            ->where('channel', $channel)
            ->where(function ($q) use ($location) {
                $q->where('location_id', $location->id)
                  ->orWhere('scope', 'GLOBAL');
            })
            ->orderByRaw("CASE WHEN scope = 'LOCATION' THEN 1 ELSE 2 END")
            ->first();

        $minMarkupType = $policy?->min_markup_type ?? 'PERCENTAGE';
        $minMarkupValue = (float) ($policy?->min_markup_value ?? 5.00); // Default 5%

        if ($minMarkupType === 'PERCENTAGE') {
            $markupAmount = round($currentHpp * ($minMarkupValue / 100), 2);
        } else {
            $markupAmount = round($minMarkupValue, 2);
        }

        // floor_price = current_unit_cost + minimum_markup
        $floorPrice = round($currentHpp + $markupAmount, 2);

        // 3. Price Rule (Market boundaries and List Price)
        $ruleQuery = PriceRule::where('product_id', $product->id)
            ->where('is_active', true)
            ->where('channel', $channel);

        if ($tier) {
            $ruleQuery->where(function ($q) use ($tier) {
                $q->where('tier_id', $tier->id)->orWhereNull('tier_id');
            })->orderByRaw("CASE WHEN tier_id IS NOT NULL THEN 1 ELSE 2 END");
        }

        $rule = $ruleQuery->first();

        $marketMinPrice = (float) ($rule?->market_min_price ?? $floorPrice);
        $marketMaxPrice = (float) ($rule?->market_max_price ?? max($marketMinPrice * 1.5, $floorPrice * 1.4));
        $listPrice = (float) ($rule?->list_price ?? max($floorPrice * 1.15, $marketMinPrice));

        // effective_min_price = max(market_min_price, floor_price)
        $effectiveMinPrice = max($marketMinPrice, $floorPrice);

        return [
            'current_unit_cost' => $currentHpp,
            'current_hpp' => $currentHpp,
            'markup_type' => $minMarkupType,
            'markup_value' => $minMarkupValue,
            'markup_amount' => $markupAmount,
            'floor_price' => $floorPrice,
            'market_min_price' => $marketMinPrice,
            'market_max_price' => $marketMaxPrice,
            'effective_min_price' => $effectiveMinPrice,
            'list_price' => $listPrice,
            'is_valid_range' => $effectiveMinPrice <= $marketMaxPrice,
        ];
    }

    /**
     * Validate offered price server-side; blocks transaction if price falls below floor
     */
    public function validateSellingPrice(
        Product $product,
        Location $location,
        float $offeredPrice,
        string $channel = 'RETAIL'
    ): array {
        $limits = $this->calculatePricingLimits($product, $location, $channel);

        if ($offeredPrice < $limits['effective_min_price']) {
            throw new \DomainException(
                "Harga tawar Rp " . number_format($offeredPrice, 0, ',', '.') . " berada di bawah batas minimum (Floor Price: Rp " . number_format($limits['effective_min_price'], 0, ',', '.') . "). Transaksi diblokir oleh server."
            );
        }

        return $limits;
    }
}
