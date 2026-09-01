<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_name' => $this->business_name,
            'owner_name' => $this->owner_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'business_type' => $this->business_type,
            'physical_address' => $this->physical_address,
            'region' => $this->region,
            'city' => $this->city,
            'kyc_status' => $this->kyc_status,
            'status' => $this->status,
            'brand_color' => $this->brandColor(),
            'logo_url' => $this->logo()?->getUrl(),
        ];
    }
}
