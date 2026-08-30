<div>
    <h2 class="text-[22px] font-light text-ink tracking-tight mb-6">Sign in</h2>

    <form wire:submit="login" class="space-y-4">
        <x-ui.input type="email" wire:model="email" label="Email" autofocus id="email" />
        @error('email') <p class="text-ruby text-[12px] -mt-3">{{ $message }}</p> @enderror

        <x-ui.input type="password" wire:model="password" label="Password" id="password" />
        @error('password') <p class="text-ruby text-[12px] -mt-3">{{ $message }}</p> @enderror

        <x-ui.button type="submit" target="login" class="w-full">
            Sign in
        </x-ui.button>
    </form>

    <p class="text-[13px] text-ink-mute mt-6 text-center">
        New merchant? <a href="{{ route('register') }}" class="text-primary hover:text-primary-deep">Register your business</a>
    </p>
</div>
