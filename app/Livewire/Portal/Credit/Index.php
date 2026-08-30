<?php

namespace App\Livewire\Portal\Credit;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.portal', ['title' => 'Working Capital'])]
class Index extends Component
{
    public function render()
    {
        abort_unless(config('dukaflow.credit_engine_enabled'), 404);

        return view('livewire.portal.credit.index', [
            'facilities' => Auth::user()->merchant->loanFacilities()->latest()->get(),
        ]);
    }
}
