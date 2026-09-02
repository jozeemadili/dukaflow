<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoreLeasePaymentResource;
use App\Http\Resources\StoreLeaseResource;
use App\Models\Branch;
use App\Models\StoreLease;
use App\Models\StoreLeasePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreLeaseController extends Controller
{
    public function index(Request $request)
    {
        $leases = StoreLease::where('merchant_id', Auth::user()->merchant_id)
            ->when($request->filled('branch_id'), fn ($q) => $q->where('branch_id', $request->integer('branch_id')))
            ->with('branch')
            ->orderByDesc('lease_start_date')
            ->get();

        return StoreLeaseResource::collection($leases);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'monthly_rent_amount' => ['required', 'numeric', 'min:0'],
            'lease_start_date' => ['required', 'date'],
            'lease_end_date' => ['nullable', 'date', 'after_or_equal:lease_start_date'],
            'notes' => ['nullable', 'string'],
        ]);

        $merchantId = Auth::user()->merchant_id;
        abort_unless(
            Branch::where('id', $data['branch_id'])->where('merchant_id', $merchantId)->exists(),
            403
        );

        $lease = StoreLease::create([
            ...$data,
            'merchant_id' => $merchantId,
            'status' => StoreLease::STATUS_ACTIVE,
        ]);

        return new StoreLeaseResource($lease->load('branch'));
    }

    public function show(Request $request, StoreLease $lease)
    {
        $this->authorizeLease($request, $lease);

        return response()->json([
            'data' => (new StoreLeaseResource($lease->load('branch')))->toArray($request),
            'payments' => StoreLeasePaymentResource::collection(
                $lease->payments()->with('recordedBy')->latest('payment_date')->get()
            ),
        ]);
    }

    public function update(Request $request, StoreLease $lease)
    {
        $this->authorizeLease($request, $lease);

        $data = $request->validate([
            'monthly_rent_amount' => ['sometimes', 'numeric', 'min:0'],
            'lease_end_date' => ['nullable', 'date', 'after_or_equal:lease_start_date'],
            'status' => ['sometimes', 'in:active,expired,terminated'],
            'notes' => ['nullable', 'string'],
        ]);

        $lease->update($data);

        return new StoreLeaseResource($lease->fresh()->load('branch'));
    }

    public function recordPayment(Request $request, StoreLease $lease)
    {
        $this->authorizeLease($request, $lease);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        StoreLeasePayment::create([
            ...$data,
            'store_lease_id' => $lease->id,
            'recorded_by' => Auth::id(),
        ]);

        return new StoreLeaseResource($lease->fresh()->load('branch'));
    }

    public function uploadContract(Request $request, StoreLease $lease)
    {
        $this->authorizeLease($request, $lease);
        $request->validate(['file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120']]);

        $lease->addMedia($request->file('file')->getRealPath())
            ->usingFileName($request->file('file')->getClientOriginalName())
            ->toMediaCollection('contract');

        return new StoreLeaseResource($lease->fresh()->load('branch'));
    }

    protected function authorizeLease(Request $request, StoreLease $lease): void
    {
        abort_unless($lease->merchant_id === $request->user()->merchant_id, 403);
    }
}
