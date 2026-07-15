<?php

use App\Services\AuthService;
use Livewire\Component;

new class extends Component {
    public bool $hasNotification = true;

    public function toggleTheme(): void
    {
        //
    }
    public function logout(AuthService $authService): void
    {
        $authService->logout();
    }

    public function getBreadcrumbsProperty(): array
    {
        return config('breadcrumbs')[request()->route()->getName()] ?? [];
    }
};
?>
<div class="flex w-full bg-zinc-50 dark:bg-slate-900 ">

    <div class="flex items-center ">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="/dashboard" icon="home"/>
            @foreach($this->breadcrumbs as $item)
                <flux:breadcrumbs.item
                >
                    {{ $item['label'] }}
                </flux:breadcrumbs.item>
            @endforeach
        </flux:breadcrumbs>
    </div>

    <div class="flex w-full items-center gap-4 px-6 py-3 justify-end  ">

        <flux:button variant="ghost" size="sm" icon="sun" wire:click="toggleTheme"/>
        <flux:button variant="ghost" size="sm" icon="question-mark-circle"/>

        <div class="relative">
            <flux:button variant="ghost" size="sm" icon="bell"/>
            @if ($hasNotification)
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            @endif
        </div>

        <flux:separator vertical class="h-8"/>

        <flux:dropdown position="bottom" align="end">
            <button class="flex items-center gap-2">
                <div class="relative">
                    @php
                        $initials = collect(explode(' ', trim(auth()->user()->name)))
                            ->filter()
                            ->map(fn ($n) => mb_strtoupper(mb_substr($n, 0, 1)))
                            ->take(2)
                            ->join('');
                    @endphp

                    <flux:avatar name="{{$initials}}" color="purple" size="sm"/>
                    <span
                        class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-zinc-900 rounded-full"></span>
                </div>
                <div class="text-left">
                    <div class="text-sm font-medium text-white">{{auth()->user()->name}}</div>
                    <div class="text-xs text-zinc-400">{{auth()->user()->cargo}}</div>
                </div>
                <flux:icon name="chevron-down" class="w-4 h-4 text-zinc-400"/>
            </button>

            <flux:menu>
                <flux:menu.item icon="arrow-right-start-on-rectangle" wire:click="logout">
                    Sair
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>

</div>
