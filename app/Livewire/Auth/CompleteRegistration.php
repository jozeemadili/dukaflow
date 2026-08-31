<?php

namespace App\Livewire\Auth;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class CompleteRegistration extends Component
{
    public string $business_name = '';

    public string $owner_name = '';

    public string $phone = '';

    public string $business_type = '';

    public string $region = '';

    public string $email = '';

    public string $google_id = '';

    public function mount()
    {
        $pending = session('google_pending');

        if (! $pending) {
            $this->redirect(route('register'), navigate: true);

            return;
        }

        if (User::where('google_id', $pending['google_id'])->orWhere('email', $pending['email'])->exists()) {
            session()->forget('google_pending');
            $this->redirect(route('login'), navigate: true);

            return;
        }

        $this->google_id = $pending['google_id'];
        $this->email = $pending['email'];
        $this->owner_name = $pending['name'] ?? '';
    }

    public function register()
    {
        $this->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'business_type' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],
        ]);

        $user = DB::transaction(function () {
            $merchant = Merchant::create([
                'business_name' => $this->business_name,
                'owner_name' => $this->owner_name,
                'phone' => $this->phone,
                'email' => $this->email,
                'business_type' => $this->business_type,
                'region' => $this->region,
                'kyc_status' => Merchant::KYC_PENDING,
            ]);

            $user = User::create([
                'name' => $this->owner_name,
                'email' => $this->email,
                'google_id' => $this->google_id,
                'password' => Hash::make(Str::random(32)),
                'user_type' => User::TYPE_MERCHANT,
                'merchant_id' => $merchant->id,
                'phone' => $this->phone,
            ]);

            $user->assignRole('merchant_owner');

            return $user;
        });

        session()->forget('google_pending');

        Auth::login($user);
        request()->session()->regenerate();

        return $this->redirect(route('portal.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.complete-registration');
    }
}
