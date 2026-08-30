<div>
    <h2 class="text-lg font-semibold mb-4">Register your business</h2>

    <form wire:submit="register" class="space-y-3">
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Business name</label>
            <input type="text" wire:model="business_name" class="w-full rounded border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
            @error('business_name') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Owner name</label>
            <input type="text" wire:model="owner_name" class="w-full rounded border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
            @error('owner_name') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Phone</label>
                <input type="text" wire:model="phone" class="w-full rounded border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                @error('phone') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Region</label>
                <input type="text" wire:model="region" class="w-full rounded border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Business type</label>
            <input type="text" wire:model="business_type" placeholder="e.g. Duka, Pharmacy, Restaurant" class="w-full rounded border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Email</label>
            <input type="email" wire:model="email" class="w-full rounded border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
            @error('email') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Password</label>
                <input type="password" wire:model="password" class="w-full rounded border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                @error('password') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Confirm password</label>
                <input type="password" wire:model="password_confirmation" class="w-full rounded border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
            </div>
        </div>

        <button type="submit" class="w-full bg-emerald-700 text-white rounded py-2 font-medium hover:bg-emerald-800 mt-2">
            Create account
        </button>
    </form>

    <p class="text-sm text-slate-500 mt-4 text-center">
        Already registered? <a href="{{ route('login') }}" class="text-emerald-700 underline">Sign in</a>
    </p>
</div>
