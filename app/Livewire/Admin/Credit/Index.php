<?php

namespace App\Livewire\Admin\Credit;

use App\Models\LoanFacility;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Credit & Lending'])]
class Index extends Component
{
    public function render()
    {
        abort_unless(config('dukaflow.credit_engine_enabled'), 404);

        return view('livewire.admin.credit.index', [
            'facilities' => LoanFacility::with('merchant')->latest()->get(),
        ]);
    }
}
