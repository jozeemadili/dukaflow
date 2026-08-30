<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin' }} · DukaFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-50 text-slate-800">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-slate-900 text-slate-200 flex-shrink-0">
            <div class="px-5 py-5 text-lg font-bold text-white border-b border-slate-800">
                DukaFlow <span class="text-emerald-400 text-xs font-normal align-top">Admin</span>
            </div>
            <nav class="p-3 space-y-1 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded hover:bg-slate-800 {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 text-white' : '' }}">Dashboard</a>

                @can('view-merchants')
                <a href="{{ route('admin.merchants.index') }}" class="block px-3 py-2 rounded hover:bg-slate-800 {{ request()->routeIs('admin.merchants.*') ? 'bg-slate-800 text-white' : '' }}">Merchants</a>
                @endcan

                @can('review-kyc')
                <a href="{{ route('admin.kyc.index') }}" class="block px-3 py-2 rounded hover:bg-slate-800 {{ request()->routeIs('admin.kyc.*') ? 'bg-slate-800 text-white' : '' }}">KYC Review</a>
                @endcan

                @can('verify-payments')
                <a href="{{ route('admin.payments.index') }}" class="block px-3 py-2 rounded hover:bg-slate-800 {{ request()->routeIs('admin.payments.*') ? 'bg-slate-800 text-white' : '' }}">Payment Verification</a>
                @endcan

                @can('manage-leads')
                <a href="{{ route('admin.leads.index') }}" class="block px-3 py-2 rounded hover:bg-slate-800 {{ request()->routeIs('admin.leads.*') ? 'bg-slate-800 text-white' : '' }}">Merchant Leads</a>
                @endcan

                @if(config('dukaflow.credit_engine_enabled'))
                    @can('manage-credit')
                    <a href="{{ route('admin.credit.index') }}" class="block px-3 py-2 rounded hover:bg-slate-800 {{ request()->routeIs('admin.credit.*') ? 'bg-slate-800 text-white' : '' }}">Credit &amp; Lending</a>
                    @endcan
                @endif

                @can('manage-users')
                <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded hover:bg-slate-800 {{ request()->routeIs('admin.users.*') ? 'bg-slate-800 text-white' : '' }}">Staff &amp; Roles</a>
                @endcan

                @can('view-audit-log')
                <a href="{{ route('admin.audit-log.index') }}" class="block px-3 py-2 rounded hover:bg-slate-800 {{ request()->routeIs('admin.audit-log.*') ? 'bg-slate-800 text-white' : '' }}">Audit Log</a>
                @endcan
            </nav>
        </aside>

        <div class="flex-1 flex flex-col">
            <header class="bg-white border-b px-6 py-3 flex items-center justify-between">
                <h1 class="text-lg font-semibold">{{ $title ?? 'Dashboard' }}</h1>
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-slate-500">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-rose-600 hover:underline">Log out</button>
                    </form>
                </div>
            </header>

            <main class="flex-1 p-6">
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
