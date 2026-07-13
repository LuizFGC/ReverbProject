<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet"/>

    <title>{{ $title ?? 'Portal do Cliente' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
    @fluxAppearance
</head>
<body class="min-h-screen bg-white dark:bg-slate-950 antialiased">
{{-- Sidebar --}}
<flux:sidebar class="w-72 shrink-0 border-r bg-zinc-50 dark:bg-slate-900 border-zinc-800 flex flex-col p-6">
    <flux:sidebar.header>
        <div class="flex items-center gap-3 pb-6 border-b border-zinc-800">
            <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center">
                <flux:icon name="lifebuoy" class="w-5 h-5 text-white"/>
            </div>
            <div>
                <p class="text-white font-semibold">Portal do Cliente</p>
                <p class="text-xs text-indigo-400">Central de Ajuda • TecSuporte</p>
            </div>
        </div>
    </flux:sidebar.header>

    <div class="flex items-center gap-3 py-5 border-b border-zinc-800">
        <div
            class="w-9 h-9 rounded-full bg-indigo-500/20 text-indigo-300 text-sm font-semibold flex items-center justify-center">
        </div>
        <div>
            <p class="text-white text-sm font-medium">{{ auth()->user()->name ?? 'Danilo Mendes' }}</p>
            <p class="text-xs text-zinc-500">{{ auth()->user()->email ?? 'danilo@startupbuilder.co' }}</p>
        </div>
    </div>

    <flux:sidebar.nav>
        <flux:sidebar.item icon="home" href="/dashboard">Meus Chamados</flux:sidebar.item>
        <flux:sidebar.item icon="ticket" badge="12" href="/tickets"> Abrir Novo Chamado</flux:sidebar.item>
    </flux:sidebar.nav>
    <flux:sidebar.spacer/>

    <flux:sidebar.nav>
        <div class="border-t border-zinc-800 pt-4 flex items-center justify-between">
            <span class="text-xs font-medium tracking-wide text-zinc-500">APARÊNCIA</span>
            <flux:icon name="sun" class="w-4 h-4 text-amber-400"/>
        </div>
        <form method="POST" action="{{ route('logout') }}" >
            @csrf
            <button class="flex items-center gap-2 text-sm text-red-400 hover:text-red-300 mt-4" type="submit">
                <flux:icon name="arrow-right-start-on-rectangle" class="w-4 h-4"/>
                Sair do Portal
            </button>
        </form>

    </flux:sidebar.nav>
</flux:sidebar>

    {{-- Main content --}}
    <flux:main class="flex-1 p-8">
        <flux:toast />
        {{ $slot }}
    </flux:main>
@livewireScripts
@fluxScripts
</body>
</html>

