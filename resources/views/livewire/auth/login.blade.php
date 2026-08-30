<div>
    <h2 class="text-lg font-semibold mb-4">Sign in</h2>

    <form wire:submit="login" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Email</label>
            <input type="email" wire:model="email" class="w-full rounded border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" autofocus>
            @error('email') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Password</label>
            <input type="password" wire:model="password" class="w-full rounded border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
            @error('password') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="w-full bg-emerald-700 text-white rounded py-2 font-medium hover:bg-emerald-800">
            Sign in
        </button>
    </form>

    <p class="text-sm text-slate-500 mt-4 text-center">
        New merchant? <a href="{{ route('register') }}" class="text-emerald-700 underline">Register your business</a>
    </p>
</div>
