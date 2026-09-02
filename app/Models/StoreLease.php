<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable([
    'merchant_id', 'branch_id', 'monthly_rent_amount',
    'lease_start_date', 'lease_end_date', 'status', 'notes',
])]
class StoreLease extends Model implements HasMedia
{
    use InteractsWithMedia;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_TERMINATED = 'terminated';

    protected function casts(): array
    {
        return [
            'monthly_rent_amount' => 'decimal:2',
            'lease_start_date' => 'date',
            'lease_end_date' => 'date',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('contract')->singleFile();
    }

    public function contract(): ?Media
    {
        return $this->getFirstMedia('contract');
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(StoreLeasePayment::class);
    }

    /**
     * Rent is due in advance: the month a lease starts in is already owed,
     * so "months due" is complete months elapsed since the start date,
     * plus the current one — capped at the lease end date once it's past.
     * Adjust here if the business instead prorates the first month or
     * bills in arrears.
     */
    public function monthsDue(): int
    {
        $through = ($this->lease_end_date && $this->lease_end_date->isPast())
            ? $this->lease_end_date
            : now();

        return max(1, (int) $this->lease_start_date->diffInMonths($through) + 1);
    }

    public function totalDue(): float
    {
        return $this->monthsDue() * (float) $this->monthly_rent_amount;
    }

    public function totalPaid(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function remainingBalance(): float
    {
        return max(0, $this->totalDue() - $this->totalPaid());
    }

    public function monthsPaid(): int
    {
        if ((float) $this->monthly_rent_amount <= 0) {
            return 0;
        }

        return (int) floor($this->totalPaid() / (float) $this->monthly_rent_amount);
    }

    public function isExpired(): bool
    {
        return $this->lease_end_date !== null && $this->lease_end_date->isPast();
    }
}
