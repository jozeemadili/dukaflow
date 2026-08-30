<?php

namespace App\Livewire\Portal\Staff;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.portal', ['title' => 'Staff'])]
class Index extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public bool $showForm = false;

    public function mount()
    {
        abort_unless(Auth::user()->can('manage-own-staff'), 403);
    }

    public function create()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'password' => Hash::make(str()->random(16)),
            'user_type' => User::TYPE_MERCHANT,
            'merchant_id' => Auth::user()->merchant_id,
        ]);

        $user->assignRole('merchant_staff');

        $this->reset(['name', 'email', 'phone', 'showForm']);
        session()->flash('status', "Staff account created for {$user->name}. They should use 'forgot password' to set their own password.");
    }

    public function render()
    {
        return view('livewire.portal.staff.index', [
            'staff' => User::where('merchant_id', Auth::user()->merchant_id)
                ->where('id', '!=', Auth::id())
                ->latest()
                ->get(),
        ]);
    }
}
