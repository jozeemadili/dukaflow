<?php

namespace App\Livewire\Admin\Merchants;

use App\Models\KycDocument;
use App\Models\Merchant;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Merchant Detail'])]
class Show extends Component
{
    public Merchant $merchant;

    public string $reviewNotes = '';

    public function mount(Merchant $merchant)
    {
        $this->merchant = $merchant;
        $this->reviewNotes = (string) $merchant->review_notes;
    }

    public function approveKyc()
    {
        $this->merchant->update([
            'kyc_status' => Merchant::KYC_APPROVED,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'review_notes' => $this->reviewNotes,
        ]);

        session()->flash('status', 'Merchant KYC approved.');
    }

    public function rejectKyc()
    {
        $this->merchant->update([
            'kyc_status' => Merchant::KYC_REJECTED,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'review_notes' => $this->reviewNotes,
        ]);

        session()->flash('status', 'Merchant KYC rejected.');
    }

    public function markUnderReview()
    {
        $this->merchant->update([
            'kyc_status' => Merchant::KYC_UNDER_REVIEW,
            'reviewed_by' => Auth::id(),
        ]);

        session()->flash('status', 'Marked as under review.');
    }

    public function approveDocument(int $documentId)
    {
        KycDocument::where('merchant_id', $this->merchant->id)->findOrFail($documentId)->update([
            'status' => KycDocument::STATUS_APPROVED,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);
    }

    public function rejectDocument(int $documentId)
    {
        KycDocument::where('merchant_id', $this->merchant->id)->findOrFail($documentId)->update([
            'status' => KycDocument::STATUS_REJECTED,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);
    }

    public function render()
    {
        $this->merchant->refresh();

        return view('livewire.admin.merchants.show', [
            'documents' => $this->merchant->kycDocuments()->latest()->get(),
            'salesTotal' => $this->merchant->salesRecords()->sum('amount'),
            'expensesTotal' => $this->merchant->expenses()->sum('amount'),
        ]);
    }
}
