<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['agent_id', 'business_name', 'contact_name', 'phone', 'status', 'notes', 'converted_merchant_id'])]
class Lead extends Model
{
    public const STATUS_NEW = 'new';

    public const STATUS_CONTACTED = 'contacted';

    public const STATUS_ONBOARDING = 'onboarding';

    public const STATUS_CONVERTED = 'converted';

    public const STATUS_LOST = 'lost';

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function convertedMerchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'converted_merchant_id');
    }
}
