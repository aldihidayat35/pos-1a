@props([
    'type' => 'green', // green, blue, purple, orange, red
    'label' => null,
    'showIcon' => true,
    'showDot' => false,
    'size' => 'md', // sm, md, lg
    'customClass' => ''
])

@php
    $normalizedType = strtolower($type);
    
    // Map aliases
    if (in_array($normalizedType, ['green', 'inbound', 'in_po', 'masuk'])) {
        $colorClass = 'bg-[#ECFDF5] text-[#065F46] border-[#A7F3D0]';
        $dotColor = 'bg-[#10B981]';
        $iconType = 'green';
        $defaultLabelId = 'Barang Masuk Normal';
        $defaultLabelEn = 'Normal Inbound';
    } elseif (in_array($normalizedType, ['blue', 'internal', 'transfer', 'distribusi'])) {
        $colorClass = 'bg-[#EFF6FF] text-[#1E40AF] border-[#BFDBFE]';
        $dotColor = 'bg-[#3B82F6]';
        $iconType = 'blue';
        $defaultLabelId = 'Pergerakan Internal';
        $defaultLabelEn = 'Internal Transfer';
    } elseif (in_array($normalizedType, ['purple', 'sales', 'sale_pos', 'sale_b2b', 'keluar'])) {
        $colorClass = 'bg-[#F5F3FF] text-[#5B21B6] border-[#DDD6FE]';
        $dotColor = 'bg-[#8B5CF6]';
        $iconType = 'purple';
        $defaultLabelId = 'Penjualan Keluar';
        $defaultLabelEn = 'Outbound Sales';
    } elseif (in_array($normalizedType, ['orange', 'emergency', 'in_emergency', 'khusus'])) {
        $colorClass = 'bg-[#FFFBEB] text-[#92400E] border-[#FDE68A]';
        $dotColor = 'bg-[#F59E0B]';
        $iconType = 'orange';
        $defaultLabelId = 'Stok Emergency';
        $defaultLabelEn = 'Emergency Stock';
    } elseif (in_array($normalizedType, ['red', 'anomaly', 'opname_adj', 'selisih', 'void'])) {
        $colorClass = 'bg-[#FEF2F2] text-[#991B1B] border-[#FECACA]';
        $dotColor = 'bg-[#EF4444]';
        $iconType = 'red';
        $defaultLabelId = 'Anomali / Selisih Minus';
        $defaultLabelEn = 'Anomaly / Discrepancy';
    } else {
        $colorClass = 'bg-[#F6F6FA] text-[#382E6B] border-[#ECEBF4]';
        $dotColor = 'bg-[#9490AD]';
        $iconType = 'neutral';
        $defaultLabelId = 'Umum';
        $defaultLabelEn = 'General';
    }

    $sizeClasses = [
        'sm' => 'text-[10px] px-2 py-0.5 gap-1',
        'md' => 'text-xs px-2.5 py-1 gap-1.5',
        'lg' => 'text-sm px-3.5 py-1.5 gap-2 font-bold'
    ][$size] ?? 'text-xs px-2.5 py-1 gap-1.5';

    $iconSizes = [
        'sm' => 'w-3 h-3',
        'md' => 'w-3.5 h-3.5',
        'lg' => 'w-4 h-4'
    ][$size] ?? 'w-3.5 h-3.5';
@endphp

<span class="inline-flex items-center font-bold rounded-full border {{ $colorClass }} {{ $sizeClasses }} {{ $customClass }} transition-colors duration-200">
    @if($showDot)
        <span class="w-2 h-2 rounded-full {{ $dotColor }} shrink-0"></span>
    @endif

    @if($showIcon)
        @if($iconType === 'green')
            <!-- Inbound Normal / Receiving Icon -->
            <svg class="{{ $iconSizes }} shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
            </svg>
        @elseif($iconType === 'blue')
            <!-- Internal Movement / Transfer Icon -->
            <svg class="{{ $iconSizes }} shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
        @elseif($iconType === 'purple')
            <!-- Outbound / POS Sales Icon -->
            <svg class="{{ $iconSizes }} shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
        @elseif($iconType === 'orange')
            <!-- Emergency / Alert Inbound Icon -->
            <svg class="{{ $iconSizes }} shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        @elseif($iconType === 'red')
            <!-- Negative Anomaly / Opname Minus Icon -->
            <svg class="{{ $iconSizes }} shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        @else
            <!-- Neutral Info Icon -->
            <svg class="{{ $iconSizes }} shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/>
            </svg>
        @endif
    @endif

    <span data-i18n-id="{{ $label ?? $defaultLabelId }}" data-i18n-en="{{ $label ?? $defaultLabelEn }}" class="badge-label-text">
        {{ $label ?? $defaultLabelId }}
    </span>
</span>
