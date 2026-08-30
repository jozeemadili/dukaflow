<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['merchant_id', 'requested_amount', 'approved_amount', 'status', 'reviewed_by'])]
class LoanFacility extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_APPLIED = 'applied';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_DECLINED = 'declined';

    public const STATUS_DISBURSED = 'disbursed';

    public const STATUS_CLOSED = 'closed';

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
