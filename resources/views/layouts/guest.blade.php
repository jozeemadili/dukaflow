<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'DukaFlow' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <span class="text-2xl font-bold text-emerald-700">DukaFlow</span>
            <p class="text-sm text-slate-500">Tanzania SME financial operating system</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            {{ $slot }}
        </div>
    </div>
    @livewireScripts
</body>
</html>
