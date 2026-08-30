<div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-4 items-start">
    <div class="space-y-4">
        @if($lastReceiptTotal)
            <div class="rounded-lg bg-primary-subtle/30 border border-primary/20 text-primary-deep px-4 py-2.5 text-[14px] flex items-center justify-between">
                <span>Sale completed — <span class="tnum font-medium">TZS {{ $lastReceiptTotal }}</span> collected.</span>
                <button wire:click="$set('lastReceiptTotal', null)" class="text-primary-deep/60 hover:text-primary-deep">&times;</button>
            </div>
        @endif

        <input
            type="text"
            wire:model.live.debounce.250ms="search"
            placeholder="Search products by name or SKU"
            class="w-full rounded-sm border border-hairline-input bg-canvas text-[14px] px-3.5 py-2.5 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"
        >

        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
            @forelse($items as $item)
                @php
                    $inCart = $cart[$item->id]['quantity'] ?? 0;
                    $remaining = (float) $item->quantity_on_hand - $inCart;
                    $outOfStock = $remaining <= 0;
                @endphp
                <button
                    type="button"
                    wire:click="addToCart({{ $item->id }})"
                    wire:loading.attr="disabled"
                    wire:target="addToCart({{ $item->id }})"
                    @disabled($outOfStock)
                    class="text-left bg-canvas border border-hairline rounded-lg p-3.5 hover:border-primary/50 hover:shadow-[0_1px_3px_rgba(0,55,112,0.08)] transition disabled:opacity-40 disabled:cursor-not-allowed relative"
                >
                    @if($inCart > 0)
                        <span class="absolute -top-2 -right-2 h-5 min-w-5 px-1 rounded-full bg-primary text-white text-[11px] font-medium flex items-center justify-center tnum">{{ rtrim(rtrim(number_format($inCart, 2, '.', ''), '0'), '.') }}</span>
                    @endif
                    <p class="text-[13px] text-ink font-medium leading-snug mb-1">{{ $item->name }}</p>
                    <p class="text-[15px] text-primary tnum font-medium">TZS {{ number_format($item->unit_price, 0) }}</p>
                    <p class="text-[11px] mt-1 {{ $outOfStock ? 'text-ruby' : 'text-ink-mute' }}">
                        {{ $outOfStock ? 'Out of stock' : rtrim(rtrim(number_format($remaining, 2, '.', ''), '0'), '.') . ' ' . $item->unit . ' left' }}
                    </p>
                </button>
            @empty
                <div class="col-span-full text-center py-12 text-ink-mute text-[13px]">
                    No sellable products found. Add items with a price in <a href="{{ route('portal.inventory.index') }}" class="text-primary hover:text-primary-deep">Inventory</a>.
                </div>
            @endforelse
        </div>
    </div>

    <div class="lg:sticky lg:top-4">
        <x-ui.card padding="p-0">
            <div class="px-5 py-4 border-b border-hairline flex items-center justify-between">
                <h2 class="text-[15px] text-ink-secondary">Current sale</h2>
                @if(count($cart) > 0)
                    <button wire:click="clearCart" class="text-[12px] text-ink-mute hover:text-ruby">Clear</button>
                @endif
            </div>

            <div class="max-h-[50vh] overflow-y-auto divide-y divide-hairline">
                @forelse($cart as $itemId => $line)
                    <div class="px-5 py-3 flex items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-[13px] text-ink font-medium truncate">{{ $line['name'] }}</p>
                            <p class="text-[12px] text-ink-mute tnum">TZS {{ number_format($line['unit_price'], 0) }} each</p>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button wire:click="decrementQty({{ $itemId }})" class="h-6 w-6 rounded-full border border-hairline text-ink-secondary hover:border-primary/50 flex items-center justify-center text-[13px]">&minus;</button>
                            <span class="w-6 text-center text-[13px] tnum">{{ rtrim(rtrim(number_format($line['quantity'], 2, '.', ''), '0'), '.') }}</span>
                            <button wire:click="incrementQty({{ $itemId }})" class="h-6 w-6 rounded-full border border-hairline text-ink-secondary hover:border-primary/50 flex items-center justify-center text-[13px]">&plus;</button>
                        </div>
                        <p class="w-16 text-right text-[13px] text-ink tnum">{{ number_format($line['quantity'] * $line['unit_price'], 0) }}</p>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-ink-mute text-[13px]">
                        Tap a product to add it to the sale.
                    </div>
                @endforelse
            </div>

            <div class="px-5 py-4 border-t border-hairline space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[13px] text-ink-mute">Total</span>
                    <span class="text-[22px] font-light tracking-tight text-ink tnum">TZS {{ number_format($this->cartTotal(), 0) }}</span>
                </div>
                <x-ui.button
                    wire:click="checkout"
                    target="checkout"
                    class="w-full"
                    :disabled="count($cart) === 0"
                >
                    Complete sale
                </x-ui.button>
            </div>
        </x-ui.card>
    </div>
</div>
