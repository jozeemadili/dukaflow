<?php

namespace App\Livewire\Admin;

use App\Models\Merchant;
use App\Models\PaymentRecord;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Dashboard'])]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'totalMerchants' => Merchant::count(),
            'pendingKyc' => Merchant::whereIn('kyc_status', [Merchant::KYC_PENDING, Merchant::KYC_UNDER_REVIEW])->count(),
            'approvedMerchants' => Merchant::where('kyc_status', Merchant::KYC_APPROVED)->count(),
            'flaggedPayments' => PaymentRecord::where('status', PaymentRecord::STATUS_FLAGGED)->count(),
            'pendingPayments' => PaymentRecord::where('status', PaymentRecord::STATUS_RECORDED)->count(),
        ]);
    }
}
