<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BranchResource;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $branches = Branch::where('merchant_id', Auth::user()->merchant_id)->orderBy('name')->get();

        return BranchResource::collection($branches);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $branch = Branch::create([...$data, 'merchant_id' => Auth::user()->merchant_id]);

        return new BranchResource($branch);
    }
}
