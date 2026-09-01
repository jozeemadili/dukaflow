<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InventoryItemResource;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $items = InventoryItem::where('merchant_id', Auth::user()->merchant_id)
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->integer('category_id')))
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->string('search').'%')
                    ->orWhere('sku', 'like', '%'.$request->string('search').'%')
                    ->orWhere('barcode', 'like', '%'.$request->string('search').'%');
            }))
            ->with(['category', 'branch'])
            ->orderBy('name')
            ->paginate($request->integer('per_page', 20));

        return InventoryItemResource::collection($items);
    }

    public function show(Request $request, InventoryItem $item)
    {
        $this->authorizeItem($request, $item);

        return new InventoryItemResource($item->load(['category', 'branch']));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255'],
            'barcode' => ['nullable', 'string', 'max:64', 'unique:inventory_items,barcode'],
            'category_id' => ['nullable', 'exists:inventory_categories,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'unit' => ['nullable', 'string', 'max:255'],
            'reorder_level' => ['nullable', 'numeric', 'min:0'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'quantity_on_hand' => ['nullable', 'numeric', 'min:0'],
            'expiry_date' => ['nullable', 'date'],
        ]);

        $item = InventoryItem::create([
            ...$data,
            'merchant_id' => Auth::user()->merchant_id,
            'barcode' => $data['barcode'] ?? InventoryItem::generateUniqueBarcode(),
            'reorder_level' => $data['reorder_level'] ?? 0,
            'quantity_on_hand' => $data['quantity_on_hand'] ?? 0,
        ]);

        return new InventoryItemResource($item);
    }

    public function update(Request $request, InventoryItem $item)
    {
        $this->authorizeItem($request, $item);

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255'],
            'barcode' => ['nullable', 'string', 'max:64', 'unique:inventory_items,barcode,'.$item->id],
            'category_id' => ['nullable', 'exists:inventory_categories,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'unit' => ['nullable', 'string', 'max:255'],
            'reorder_level' => ['nullable', 'numeric', 'min:0'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'expiry_date' => ['nullable', 'date'],
        ]);

        $item->update($data);

        return new InventoryItemResource($item->fresh());
    }

    public function destroy(Request $request, InventoryItem $item)
    {
        $this->authorizeItem($request, $item);
        $item->delete();

        return response()->json(['message' => 'Product deleted.']);
    }

    /**
     * Barcode scanning: the Flutter app scans a code and hits this to find the
     * matching product. A 404 tells the app to offer "add new product" instead.
     */
    public function lookupBarcode(Request $request, string $code)
    {
        $item = InventoryItem::where('merchant_id', Auth::user()->merchant_id)
            ->where('barcode', $code)
            ->with(['category', 'branch'])
            ->first();

        if (! $item) {
            return response()->json(['message' => 'No product found for this barcode.'], 404);
        }

        return new InventoryItemResource($item);
    }

    public function generateBarcode(Request $request, InventoryItem $item)
    {
        $this->authorizeItem($request, $item);
        $item->update(['barcode' => InventoryItem::generateUniqueBarcode()]);

        return new InventoryItemResource($item->fresh());
    }

    /**
     * Product photo upload — the same endpoint whether the Flutter app sourced
     * the image from the camera or the gallery; both arrive here as a normal
     * multipart file.
     */
    public function uploadImage(Request $request, InventoryItem $item)
    {
        $this->authorizeItem($request, $item);
        $request->validate(['image' => ['required', 'image', 'max:4096']]);

        $item->addMedia($request->file('image')->getRealPath())
            ->usingFileName($request->file('image')->getClientOriginalName())
            ->toMediaCollection('image');

        return new InventoryItemResource($item->fresh());
    }

    public function stockMovement(Request $request, InventoryItem $item)
    {
        $this->authorizeItem($request, $item);

        $data = $request->validate([
            'type' => ['required', 'in:in,out,adjustment'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
        ]);

        StockMovement::create([
            'merchant_id' => $item->merchant_id,
            'inventory_item_id' => $item->id,
            'type' => $data['type'],
            'quantity' => $data['quantity'],
            'reference' => 'mobile_app',
            'movement_date' => now()->toDateString(),
            'recorded_by' => Auth::id(),
        ]);

        $delta = $data['type'] === 'out' ? -$data['quantity'] : $data['quantity'];
        $item->increment('quantity_on_hand', $delta);

        return new InventoryItemResource($item->fresh());
    }

    public function categories(Request $request)
    {
        return InventoryCategory::where('merchant_id', Auth::user()->merchant_id)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    protected function authorizeItem(Request $request, InventoryItem $item): void
    {
        abort_unless($item->merchant_id === $request->user()->merchant_id, 403);
    }
}
