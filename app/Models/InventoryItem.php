<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable(['merchant_id', 'name', 'sku', 'barcode', 'unit', 'quantity_on_hand', 'reorder_level', 'unit_cost', 'unit_price'])]
class InventoryItem extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected function casts(): array
    {
        return [
            'quantity_on_hand' => 'decimal:2',
            'reorder_level' => 'decimal:2',
            'unit_cost' => 'decimal:2',
            'unit_price' => 'decimal:2',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function stockReceiptItems(): HasMany
    {
        return $this->hasMany(StockReceiptItem::class);
    }

    public function isLowStock(): bool
    {
        return $this->quantity_on_hand <= $this->reorder_level;
    }

    public function image(): ?Media
    {
        return $this->getFirstMedia('image');
    }
}
