<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Portal' }} · DukaFlow</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon-180.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-canvas-soft text-ink font-sans antialiased">
    <div class="flex min-h-screen gap-4 p-4">
        <aside class="w-60 shrink-0">
            <div class="sticky top-4 bg-brand-dark rounded-xl p-4 flex flex-col h-[calc(100vh-2rem)]">
                <div class="px-2 py-2 mb-4">
                    <span class="text-[17px] font-light tracking-tight text-white">Duka<span class="font-medium text-primary-soft">Flow</span></span>
                    <p class="text-[11px] text-white/40 mt-0.5 uppercase tracking-wide truncate">{{ auth()->user()->merchant?->business_name }}</p>
                </div>

                <nav class="space-y-1 flex-1 overflow-y-auto">
                    <a href="{{ route('portal.dashboard') }}" class="icon-dock-item {{ request()->routeIs('portal.dashboard') ? 'is-active' : '' }}">
                        <x-icon.dashboard class="h-4 w-4 shrink-0" /> Dashboard
                    </a>
                    <a href="{{ route('portal.pos.index') }}" class="icon-dock-item {{ request()->routeIs('portal.pos.*') ? 'is-active' : '' }}">
                        <x-icon.pos class="h-4 w-4 shrink-0" /> Point of Sale
                    </a>
                    <a href="{{ route('portal.sales.index') }}" class="icon-dock-item {{ request()->routeIs('portal.sales.*') ? 'is-active' : '' }}">
                        <x-icon.sales class="h-4 w-4 shrink-0" /> Sales
                    </a>
                    <a href="{{ route('portal.expenses.index') }}" class="icon-dock-item {{ request()->routeIs('portal.expenses.*') ? 'is-active' : '' }}">
                        <x-icon.expenses class="h-4 w-4 shrink-0" /> Expenses
                    </a>
                    <a href="{{ route('portal.inventory.index') }}" class="icon-dock-item {{ request()->routeIs('portal.inventory.*') ? 'is-active' : '' }}">
                        <x-icon.inventory class="h-4 w-4 shrink-0" /> Inventory
                    </a>
                    <a href="{{ route('portal.stock-receipts.index') }}" class="icon-dock-item {{ request()->routeIs('portal.stock-receipts.*') ? 'is-active' : '' }}">
                        <x-icon.stock-receipt class="h-4 w-4 shrink-0" /> Stock Receipts
                    </a>
                    <a href="{{ route('portal.suppliers.index') }}" class="icon-dock-item {{ request()->routeIs('portal.suppliers.*') ? 'is-active' : '' }}">
                        <x-icon.suppliers class="h-4 w-4 shrink-0" /> Suppliers
                    </a>
                    <a href="{{ route('portal.customers.index') }}" class="icon-dock-item {{ request()->routeIs('portal.customers.*') ? 'is-active' : '' }}">
                        <x-icon.customer class="h-4 w-4 shrink-0" /> Customers
                    </a>
                    <a href="{{ route('portal.payments.index') }}" class="icon-dock-item {{ request()->routeIs('portal.payments.*') ? 'is-active' : '' }}">
                        <x-icon.receipt class="h-4 w-4 shrink-0" /> Payments
                    </a>
                    <a href="{{ route('portal.kyc.index') }}" class="icon-dock-item {{ request()->routeIs('portal.kyc.*') ? 'is-active' : '' }}">
                        <x-icon.shield class="h-4 w-4 shrink-0" /> KYC Documents
                    </a>

                    @if(config('dukaflow.credit_engine_enabled'))
                        @can('apply-credit')
                        <a href="{{ route('portal.credit.index') }}" class="icon-dock-item {{ request()->routeIs('portal.credit.*') ? 'is-active' : '' }}">
                            <x-icon.credit class="h-4 w-4 shrink-0" /> Working Capital
                        </a>
                        @endcan
                    @endif

                    @can('manage-own-staff')
                    <a href="{{ route('portal.staff.index') }}" class="icon-dock-item {{ request()->routeIs('portal.staff.*') ? 'is-active' : '' }}">
                        <x-icon.users class="h-4 w-4 shrink-0" /> Staff
                    </a>
                    @endcan

                    @can('manage-discount-limits')
                    <a href="{{ route('portal.discount-limits.index') }}" class="icon-dock-item {{ request()->routeIs('portal.discount-limits.*') ? 'is-active' : '' }}">
                        <x-icon.percent class="h-4 w-4 shrink-0" /> Discount Limits
                    </a>
                    @endcan
                </nav>

                <div class="pt-3 mt-3 border-t border-white/10">
                    <p class="px-2 text-[13px] text-white/80 truncate">{{ auth()->user()->name }}</p>
                    <form method="POST" action="{{ route('logout') }}" class="mt-1">
                        @csrf
                        <button type="submit" class="icon-dock-item w-full text-left text-ruby/80 hover:text-ruby hover:bg-ruby/10">
                            <x-icon.logout class="h-4 w-4 shrink-0" /> Log out
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex-1 min-w-0">
            <header class="flex items-center justify-between mb-6 px-1 pt-2">
                <h1 class="text-[28px] font-light tracking-tight text-ink">{{ $title ?? 'Dashboard' }}</h1>
            </header>

            @if(auth()->user()->merchant && auth()->user()->merchant->kyc_status !== \App\Models\Merchant::KYC_APPROVED)
                <div class="mb-4 rounded-lg bg-canvas-cream border border-lemon/20 text-lemon px-4 py-2.5 text-[14px]">
                    Your business verification is <strong>{{ str_replace('_', ' ', auth()->user()->merchant->kyc_status) }}</strong>.
                    Upload your KYC documents to get fully verified. <a href="{{ route('portal.kyc.index') }}" class="underline">Go to KYC Documents</a>.
                </div>
            @endif

            @if(session('status'))
                <div class="mb-4 rounded-lg bg-primary-subtle/30 border border-primary/20 text-primary-deep px-4 py-2.5 text-[14px]">
                    {{ session('status') }}
                </div>
            @endif

            <main class="pb-10">
                {{ $slot }}
            </main>
        </div>
    </div>
    @livewireScripts
</body>
</html>
