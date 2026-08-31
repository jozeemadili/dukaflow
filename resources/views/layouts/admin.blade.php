<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin' }} · DukaFlow</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon-180.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-canvas-soft text-ink font-sans antialiased">
    <div class="flex min-h-screen gap-4 p-4">
        <aside class="w-60 shrink-0">
            <div class="sticky top-4 bg-brand-dark rounded-xl p-4 flex flex-col h-[calc(100vh-2rem)]">
                <div class="px-2 py-2 mb-4 flex items-center gap-2">
                    <img src="{{ asset('images/favicon-512.png') }}" alt="DukaFlow" class="h-7 w-7 rounded-md shrink-0">
                    <div class="min-w-0">
                        <span class="text-[17px] font-light tracking-tight text-white">Duka<span class="font-medium text-primary-soft">Flow</span></span>
                        <p class="text-[11px] text-white/40 mt-0.5 uppercase tracking-wide">Admin console</p>
                    </div>
                </div>

                <nav class="space-y-1 flex-1 overflow-y-auto">
                    <a href="{{ route('admin.dashboard') }}" class="icon-dock-item {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                        <x-icon.dashboard class="h-4 w-4 shrink-0" /> Dashboard
                    </a>

                    @can('view-merchants')
                    <a href="{{ route('admin.merchants.index') }}" class="icon-dock-item {{ request()->routeIs('admin.merchants.*') ? 'is-active' : '' }}">
                        <x-icon.merchants class="h-4 w-4 shrink-0" /> Merchants
                    </a>
                    @endcan

                    @can('review-kyc')
                    <a href="{{ route('admin.kyc.index') }}" class="icon-dock-item {{ request()->routeIs('admin.kyc.*') ? 'is-active' : '' }}">
                        <x-icon.shield class="h-4 w-4 shrink-0" /> KYC Review
                    </a>
                    @endcan

                    @can('verify-payments')
                    <a href="{{ route('admin.payments.index') }}" class="icon-dock-item {{ request()->routeIs('admin.payments.*') ? 'is-active' : '' }}">
                        <x-icon.receipt class="h-4 w-4 shrink-0" /> Payment Verification
                    </a>
                    @endcan

                    @can('manage-leads')
                    <a href="{{ route('admin.leads.index') }}" class="icon-dock-item {{ request()->routeIs('admin.leads.*') ? 'is-active' : '' }}">
                        <x-icon.target class="h-4 w-4 shrink-0" /> Merchant Leads
                    </a>
                    @endcan

                    @if(config('dukaflow.credit_engine_enabled'))
                        @can('manage-credit')
                        <a href="{{ route('admin.credit.index') }}" class="icon-dock-item {{ request()->routeIs('admin.credit.*') ? 'is-active' : '' }}">
                            <x-icon.credit class="h-4 w-4 shrink-0" /> Credit &amp; Lending
                        </a>
                        @endcan
                    @endif

                    @can('manage-users')
                    <a href="{{ route('admin.users.index') }}" class="icon-dock-item {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">
                        <x-icon.users class="h-4 w-4 shrink-0" /> Staff &amp; Roles
                    </a>
                    @endcan

                    @can('view-audit-log')
                    <a href="{{ route('admin.audit-log.index') }}" class="icon-dock-item {{ request()->routeIs('admin.audit-log.*') ? 'is-active' : '' }}">
                        <x-icon.log class="h-4 w-4 shrink-0" /> Audit Log
                    </a>
                    @endcan
                </nav>

                <div class="pt-3 mt-3 border-t border-white/10">
                    <p class="px-2 text-[13px] text-white/80 truncate">{{ auth()->user()->name }}</p>
                    @if(auth()->user()->roleLabel())
                        <p class="px-2 text-[11px] text-primary-soft/80 uppercase tracking-wide">{{ auth()->user()->roleLabel() }}</p>
                    @endif
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
