<?php

namespace App\Livewire\Portal\StoreLeases;

use App\Models\Branch;
use App\Models\StoreLease;
use App\Models\StoreLeasePayment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.portal', ['title' => 'Store Leases'])]
class Index extends Component
{
    use WithFileUploads;

    public bool $showLeaseForm = false;

    public string $branch_id = '';

    public string $monthly_rent_amount = '';

    public string $lease_start_date = '';

    public string $lease_end_date = '';

    public string $notes = '';

    public ?int $expandedLeaseId = null;

    public string $paymentAmount = '';

    public string $paymentDate = '';

    public string $paymentNotes = '';

    public $contractFile;

    public function toggleExpand(int $leaseId): void
    {
        $this->expandedLeaseId = $this->expandedLeaseId === $leaseId ? null : $leaseId;
        $this->paymentAmount = '';
        $this->paymentDate = now()->toDateString();
        $this->paymentNotes = '';
        $this->contractFile = null;
    }

    public function saveLease(): void
    {
        $data = $this->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'monthly_rent_amount' => ['required', 'numeric', 'min:0'],
            'lease_start_date' => ['required', 'date'],
            'lease_end_date' => ['nullable', 'date', 'after_or_equal:lease_start_date'],
            'notes' => ['nullable', 'string'],
        ]);

        $merchantId = Auth::user()->merchant_id;
        abort_unless(Branch::where('id', $data['branch_id'])->where('merchant_id', $merchantId)->exists(), 403);

        StoreLease::create([
            ...$data,
            'lease_end_date' => $data['lease_end_date'] ?: null,
            'notes' => $data['notes'] ?: null,
            'merchant_id' => $merchantId,
            'status' => StoreLease::STATUS_ACTIVE,
        ]);

        $this->reset(['branch_id', 'monthly_rent_amount', 'lease_start_date', 'lease_end_date', 'notes', 'showLeaseForm']);
        session()->flash('status', 'Lease created.');
    }

    public function recordPayment(int $leaseId): void
    {
        $lease = StoreLease::where('merchant_id', Auth::user()->merchant_id)->findOrFail($leaseId);

        $data = $this->validate([
            'paymentAmount' => ['required', 'numeric', 'min:0.01'],
            'paymentDate' => ['required', 'date'],
            'paymentNotes' => ['nullable', 'string'],
        ]);

        StoreLeasePayment::create([
            'store_lease_id' => $lease->id,
            'amount' => $data['paymentAmount'],
            'payment_date' => $data['paymentDate'],
            'notes' => $data['paymentNotes'] ?: null,
            'recorded_by' => Auth::id(),
        ]);

        $this->paymentAmount = '';
        $this->paymentNotes = '';
        session()->flash('status', 'Payment recorded.');
    }

    public function uploadContract(int $leaseId): void
    {
        $lease = StoreLease::where('merchant_id', Auth::user()->merchant_id)->findOrFail($leaseId);

        $this->validate(['contractFile' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120']]);

        $lease->addMedia($this->contractFile->getRealPath())
            ->usingFileName($this->contractFile->getClientOriginalName())
            ->toMediaCollection('contract');

        $this->contractFile = null;
        session()->flash('status', 'Contract uploaded.');
    }

    public function updateStatus(int $leaseId, string $status): void
    {
        if (! in_array($status, [StoreLease::STATUS_ACTIVE, StoreLease::STATUS_EXPIRED, StoreLease::STATUS_TERMINATED], true)) {
            return;
        }

        StoreLease::where('merchant_id', Auth::user()->merchant_id)->findOrFail($leaseId)->update(['status' => $status]);
        session()->flash('status', 'Lease status updated.');
    }

    public function render()
    {
        $merchantId = Auth::user()->merchant_id;

        $leases = StoreLease::where('merchant_id', $merchantId)
            ->with(['branch', 'payments' => fn ($q) => $q->latest('payment_date')])
            ->orderByDesc('lease_start_date')
            ->get();

        return view('livewire.portal.store-leases.index', [
            'leases' => $leases,
            'branches' => Branch::where('merchant_id', $merchantId)->orderBy('name')->get(),
        ]);
    }
}
