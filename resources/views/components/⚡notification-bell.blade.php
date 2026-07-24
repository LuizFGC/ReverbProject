<?php

use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {

    public function getListeners(): array
    {
        return [
            "echo-notification:App.Models.User." . auth()->user()->id . ",notification" => 'onNotificationReceived',
        ];
    }

    #[Computed]
    public function notifications()
    {
        return auth()->user()
            ->notifications()
            ->latest()
            ->take(10)
            ->get();
    }

    #[Computed]
    public function hasNotification(): bool
    {
        return auth()->user()->unreadNotifications()->exists();
    }

    #[Computed]
    public function unreadCount(): int
    {
        return auth()->user()->unreadNotifications()->count();
    }

    public function onNotificationReceived(): void
    {
        unset($this->notifications, $this->hasNotification, $this->unreadCount);
    }

    public function markAsRead(string $notificationId): void
    {
        $notification = auth()->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        unset($this->notifications, $this->hasNotification, $this->unreadCount);
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();

        unset($this->notifications, $this->hasNotification, $this->unreadCount);
    }

};
?>
<div>

    <flux:dropdown position="bottom" align="end">
        <div class="relative">
            <flux:button variant="ghost" size="sm" icon="bell"/>
            @if ($this->hasNotification)
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            @endif
        </div>

        <flux:menu class="w-80">
            <div class="flex items-center justify-between px-3 py-2">
                <span class="text-sm font-semibold">Notificações</span>
                @if ($this->unreadCount > 0)
                    <button
                        wire:click="markAllAsRead"
                        class="text-xs text-purple-500 hover:underline"
                    >
                        Marcar todas como lidas
                    </button>
                @endif
            </div>

            <flux:menu.separator/>

            @forelse ($this->notifications as $notification)
                <flux:menu.item
                    wire:click="markAsRead('{{ $notification->id }}')"
                    wire:key="notification-{{ $notification->id }}"
                    class="{{ is_null($notification->read_at) ? 'font-semibold' : 'opacity-60' }}"
                >
                    <div class="flex flex-col gap-0.5 border-b py-2 ">
                        <a href="/tickets/{{$notification->data['ticket_id']}}">
                        <span class="text-sm">{{ $notification->data['message'] ?? '' }}</span>
                        <span class="text-xs text-zinc-400">
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                        </a>
                    </div>
                </flux:menu.item>
            @empty
                <div class="px-3 py-4 text-sm text-zinc-400 text-center">
                    Nenhuma notificação
                </div>
            @endforelse
        </flux:menu>
    </flux:dropdown>
</div>
