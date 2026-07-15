<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

        <title>{{ $title ?? config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
        @fluxAppearance
    </head>
    <body class="min-h-screen bg-white dark:bg-slate-950 antialiased">

    <flux:sidebar sticky collapsible class="bg-zinc-50 dark:bg-slate-900 border-r border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.header>
            <flux:sidebar.brand
                href="/dashboard"
                logo="https://fluxui.dev/img/demo/logo.png"
                logo:dark="https://fluxui.dev/img/demo/dark-mode-logo.png"
                name="TecSuporte."
            />
            <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
        </flux:sidebar.header>
        <flux:sidebar.nav>
            <flux:sidebar.item icon="home" href="/dashboard" >Dashboard</flux:sidebar.item>
            <flux:sidebar.item icon="ticket" badge="12" href="/tickets">Tickets</flux:sidebar.item>
            <flux:sidebar.item icon="user-group" href="/time">Agentes / Time</flux:sidebar.item>
        </flux:sidebar.nav>
        <flux:sidebar.spacer />
        <flux:sidebar.nav>
            <flux:sidebar.item icon="cog-6-tooth" href="/settings">Settings</flux:sidebar.item>
            <flux:sidebar.item icon="information-circle" href="#">Help</flux:sidebar.item>
        </flux:sidebar.nav>
        <flux:dropdown position="top" align="start" class="max-lg:hidden">
            @php
                $initials = collect(explode(' ', trim(auth()->user()->name)))
                    ->filter()
                    ->map(fn ($n) => mb_strtoupper(mb_substr($n, 0, 1)))
                    ->take(2)
                    ->join('');
            @endphp
            <flux:sidebar.profile avatar="{{auth()->user()->name}}" name="{{auth()->user()->name}}" />
            <flux:menu>
                @csrf
                    <form method="POST" action="{{ route('logout') }}" >
                        <button class="flex items-center gap-2 text-sm text-red-400 hover:text-red-300 mt-4" type="submit">
                            <flux:icon name="arrow-right-start-on-rectangle" class="w-4 h-4"/>
                            Sair do Portal
                        </button>
                    </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>
    <flux:header sticky class="flex justify-end  bg-zinc-50 dark:bg-slate-900 border-b border-zinc-200 dark:border-zinc-700">
        <livewire:header/>
    </flux:header>
    <flux:main>
        <flux:toast />
        {{$slot}}
    </flux:main>
    @fluxScripts
    </body>
</html>
