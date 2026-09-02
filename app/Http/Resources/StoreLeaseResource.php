<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreLeaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch' => $this->whenLoaded('branch', fn () => $this->branch ? [
                'id' => $this->branch->id,
                'name' => $this->branch->name,
            ] : null),
            'monthly_rent_amount' => (float) $this->monthly_rent_amount,
            'lease_start_date' => $this->lease_start_date?->toDateString(),
            'lease_end_date' => $this->lease_end_date?->toDateString(),
            'status' => $this->status,
            'notes' => $this->notes,
            'months_due' => $this->monthsDue(),
            'months_paid' => $this->monthsPaid(),
            'total_due' => $this->totalDue(),
            'total_paid' => $this->totalPaid(),
            'remaining_balance' => $this->remainingBalance(),
            'is_expired' => $this->isExpired(),
            'contract_url' => $this->contract()?->getUrl(),
        ];
    }
}
