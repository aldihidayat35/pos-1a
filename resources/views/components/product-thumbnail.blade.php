@props([
    'product' => null,
    'imageUrl' => null,
    'name' => null,
    'sku' => null,
    'category' => null,
    'size' => 'md',
    'showModal' => true,
    'class' => ''
])

@php
    $prodName = $name ?? ($product->name ?? 'Produk');
    $prodSku = $sku ?? ($product->sku ?? '');
    $prodCategory = $category ?? ($product->category->name ?? '');
    $prodImg = $imageUrl ?? ($product->image_url ?? null);
    $editUrl = $product && isset($product->id) ? route('master.products.edit', $product->id) : null;

    $sizeClasses = match($size) {
        'xs' => 'w-7 h-7 rounded-lg text-[10px]',
        'sm' => 'w-8 h-8 rounded-lg text-xs',
        'lg' => 'w-12 h-12 rounded-2xl text-base',
        'xl' => 'w-16 h-16 rounded-2xl text-lg',
        default => 'w-10 h-10 rounded-xl text-sm',
    };

    $iconSize = match($size) {
        'xs' => 'w-3.5 h-3.5',
        'sm' => 'w-4 h-4',
        'lg' => 'w-6 h-6',
        'xl' => 'w-8 h-8',
        default => 'w-5 h-5',
    };

    // Soft palette based on SKU or Name hash
    $colorOptions = [
        ['bg' => 'bg-emerald-50 text-emerald-600 border-emerald-100', 'hover' => 'group-hover:border-emerald-300'],
        ['bg' => 'bg-indigo-50 text-indigo-600 border-indigo-100', 'hover' => 'group-hover:border-indigo-300'],
        ['bg' => 'bg-amber-50 text-amber-600 border-amber-100', 'hover' => 'group-hover:border-amber-300'],
        ['bg' => 'bg-rose-50 text-rose-600 border-rose-100', 'hover' => 'group-hover:border-rose-300'],
        ['bg' => 'bg-cyan-50 text-cyan-600 border-cyan-100', 'hover' => 'group-hover:border-cyan-300'],
        ['bg' => 'bg-purple-50 text-purple-600 border-purple-100', 'hover' => 'group-hover:border-purple-300'],
    ];
    $colorIndex = abs(crc32($prodSku ?: $prodName)) % count($colorOptions);
    $palette = $colorOptions[$colorIndex];
@endphp

<div 
  class="relative inline-flex shrink-0 items-center justify-center group {{ $showModal ? 'cursor-pointer' : '' }} {{ $class }}"
  @if($showModal)
    onclick="event.stopPropagation(); openProductImageModal({
      name: {{ json_encode($prodName) }},
      sku: {{ json_encode($prodSku) }},
      category: {{ json_encode($prodCategory) }},
      imageUrl: {{ json_encode($prodImg) }},
      editUrl: {{ json_encode($editUrl) }}
    })"
    title="Klik untuk memperbesar foto: {{ $prodName }}"
  @endif
>
  @if($prodImg)
    <img 
      src="{{ $prodImg }}" 
      alt="{{ $prodName }}" 
      class="{{ $sizeClasses }} object-cover border border-slate-200/80 shadow-2xs group-hover:shadow-md group-hover:scale-105 transition-all duration-150"
      loading="lazy"
    />
    @if($showModal)
      <div class="absolute inset-0 bg-slate-900/20 opacity-0 group-hover:opacity-100 {{ $sizeClasses }} flex items-center justify-center text-white transition-opacity duration-150">
        <svg class="w-3.5 h-3.5 drop-shadow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
        </svg>
      </div>
    @endif
  @else
    <div class="{{ $sizeClasses }} {{ $palette['bg'] }} border shadow-2xs group-hover:scale-105 {{ $palette['hover'] }} flex items-center justify-center transition-all duration-150 font-semibold select-none">
      <svg class="{{ $iconSize }} opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
      </svg>
    </div>
  @endif
</div>
