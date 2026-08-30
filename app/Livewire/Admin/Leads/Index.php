<?php

namespace App\Livewire\Admin\Leads;

use App\Models\Lead;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Merchant Leads'])]
class Index extends Component
{
    public string $business_name = '';

    public string $contact_name = '';

    public string $phone = '';

    public bool $showForm = false;

    public function create()
    {
        $this->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        Lead::create([
            'agent_id' => Auth::id(),
            'business_name' => $this->business_name,
            'contact_name' => $this->contact_name,
            'phone' => $this->phone,
            'status' => Lead::STATUS_NEW,
        ]);

        $this->reset(['business_name', 'contact_name', 'phone', 'showForm']);
        session()->flash('status', 'Lead added.');
    }

    public function updateStatus(int $leadId, string $status)
    {
        Lead::findOrFail($leadId)->update(['status' => $status]);
    }

    public function render()
    {
        return view('livewire.admin.leads.index', [
            'leads' => Lead::with('agent')->latest()->get(),
        ]);
    }
}
