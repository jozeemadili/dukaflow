<div>
    <h2 class="text-[22px] font-light text-ink tracking-tight mb-6">Register your business</h2>

    <form wire:submit="register" class="space-y-3">
        <x-ui.input wire:model="business_name" label="Business name" id="business_name" />
        @error('business_name') <p class="text-ruby text-[12px] -mt-2">{{ $message }}</p> @enderror

        <x-ui.input wire:model="owner_name" label="Owner name" id="owner_name" />
        @error('owner_name') <p class="text-ruby text-[12px] -mt-2">{{ $message }}</p> @enderror

        <div class="grid grid-cols-2 gap-3">
            <div>
                <x-ui.input wire:model="phone" label="Phone" id="phone" />
                @error('phone') <p class="text-ruby text-[12px] mt-1">{{ $message }}</p> @enderror
            </div>
            <x-ui.input wire:model="region" label="Region" id="region" />
        </div>

        <x-ui.input wire:model="business_type" label="Business type" placeholder="e.g. Duka, Pharmacy, Restaurant" id="business_type" />

        <x-ui.input type="email" wire:model="email" label="Email" id="email" />
        @error('email') <p class="text-ruby text-[12px] -mt-2">{{ $message }}</p> @enderror

        <div class="grid grid-cols-2 gap-3">
            <div>
                <x-ui.input type="password" wire:model="password" label="Password" id="password" />
                @error('password') <p class="text-ruby text-[12px] mt-1">{{ $message }}</p> @enderror
            </div>
            <x-ui.input type="password" wire:model="password_confirmation" label="Confirm password" id="password_confirmation" />
        </div>

        <x-ui.button type="submit" target="register" class="w-full mt-2">
            Create account
        </x-ui.button>
    </form>

    <p class="text-[13px] text-ink-mute mt-6 text-center">
        Already registered? <a href="{{ route('login') }}" class="text-primary hover:text-primary-deep">Sign in</a>
    </p>
</div>
