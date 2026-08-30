<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Portal' }} · DukaFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-50 text-slate-800">
    <div class="flex min-h-screen">
        <aside class="w-60 bg-emerald-800 text-emerald-50 flex-shrink-0">
            <div class="px-5 py-5 text-lg font-bold border-b border-emerald-700">
                DukaFlow
            </div>
            <nav class="p-3 space-y-1 text-sm">
                <a href="{{ route('portal.dashboard') }}" class="block px-3 py-2 rounded hover:bg-emerald-700 {{ request()->routeIs('portal.dashboard') ? 'bg-emerald-700' : '' }}">Dashboard</a>
                <a href="{{ route('portal.sales.index') }}" class="block px-3 py-2 rounded hover:bg-emerald-700 {{ request()->routeIs('portal.sales.*') ? 'bg-emerald-700' : '' }}">Sales</a>
                <a href="{{ route('portal.expenses.index') }}" class="block px-3 py-2 rounded hover:bg-emerald-700 {{ request()->routeIs('portal.expenses.*') ? 'bg-emerald-700' : '' }}">Expenses</a>
                <a href="{{ route('portal.inventory.index') }}" class="block px-3 py-2 rounded hover:bg-emerald-700 {{ request()->routeIs('portal.inventory.*') ? 'bg-emerald-700' : '' }}">Inventory</a>
                <a href="{{ route('portal.suppliers.index') }}" class="block px-3 py-2 rounded hover:bg-emerald-700 {{ request()->routeIs('portal.suppliers.*') ? 'bg-emerald-700' : '' }}">Suppliers</a>
                <a href="{{ route('portal.payments.index') }}" class="block px-3 py-2 rounded hover:bg-emerald-700 {{ request()->routeIs('portal.payments.*') ? 'bg-emerald-700' : '' }}">Payments</a>
                <a href="{{ route('portal.kyc.index') }}" class="block px-3 py-2 rounded hover:bg-emerald-700 {{ request()->routeIs('portal.kyc.*') ? 'bg-emerald-700' : '' }}">KYC Documents</a>

                @if(config('dukaflow.credit_engine_enabled'))
                    @can('apply-credit')
                    <a href="{{ route('portal.credit.index') }}" class="block px-3 py-2 rounded hover:bg-emerald-700 {{ request()->routeIs('portal.credit.*') ? 'bg-emerald-700' : '' }}">Working Capital</a>
                    @endcan
                @endif

                @can('manage-own-staff')
                <a href="{{ route('portal.staff.index') }}" class="block px-3 py-2 rounded hover:bg-emerald-700 {{ request()->routeIs('portal.staff.*') ? 'bg-emerald-700' : '' }}">Staff</a>
                @endcan
            </nav>
        </aside>

        <div class="flex-1 flex flex-col">
            <header class="bg-white border-b px-6 py-3 flex items-center justify-between">
                <h1 class="text-lg font-semibold">{{ $title ?? 'Dashboard' }}</h1>
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-slate-500">{{ auth()->user()->merchant?->business_name }} · {{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-rose-600 hover:underline">Log out</button>
                    </form>
                </div>
            </header>

            <main class="flex-1 p-6">
                @if(auth()->user()->merchant && auth()->user()->merchant->kyc_status !== \App\Models\Merchant::KYC_APPROVED)
                    <div class="mb-4 rounded bg-amber-50 border border-amber-200 text-amber-800 px-4 py-2 text-sm">
                        Your business verification is <strong>{{ str_replace('_', ' ', auth()->user()->merchant->kyc_status) }}</strong>.
                        Upload your KYC documents to get fully verified. <a href="{{ route('portal.kyc.index') }}" class="underline">Go to KYC Documents</a>.
                    </div>
                @endif

                @if(session('status'))
                    <div class="mb-4 rounded bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-2 text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
    @livewireScripts
</body>
</html>
