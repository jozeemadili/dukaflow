<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

#[Fillable([
    'business_name', 'owner_name', 'phone', 'email', 'business_type',
    'tin_number', 'physical_address', 'region', 'city',
    'kyc_status', 'subscription_tier', 'status',
    'created_by_agent_id', 'reviewed_by', 'reviewed_at', 'review_notes',
])]
class Merchant extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['business_name', 'kyc_status', 'status', 'subscription_tier', 'reviewed_by'])
            ->logOnlyDirty()
            ->useLogName('merchant');
    }

    public const KYC_PENDING = 'pending';

    public const KYC_UNDER_REVIEW = 'under_review';

    public const KYC_APPROVED = 'approved';

    public const KYC_REJECTED = 'rejected';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_SUSPENDED = 'suspended';

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function kycDocuments(): HasMany
    {
        return $this->hasMany(KycDocument::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function salesRecords(): HasMany
    {
        return $this->hasMany(SalesRecord::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function stockReceipts(): HasMany
    {
        return $this->hasMany(StockReceipt::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function discountLimits(): HasMany
    {
        return $this->hasMany(DiscountLimit::class);
    }

    public function expenseCategories(): HasMany
    {
        return $this->hasMany(ExpenseCategory::class);
    }

    public function inventoryCategories(): HasMany
    {
        return $this->hasMany(InventoryCategory::class);
    }

    public function paymentRecords(): HasMany
    {
        return $this->hasMany(PaymentRecord::class);
    }

    public function cashFlowSnapshots(): HasMany
    {
        return $this->hasMany(CashFlowSnapshot::class);
    }

    public function creditScores(): HasMany
    {
        return $this->hasMany(CreditScore::class);
    }

    public function loanFacilities(): HasMany
    {
        return $this->hasMany(LoanFacility::class);
    }

    public function createdByAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_agent_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
