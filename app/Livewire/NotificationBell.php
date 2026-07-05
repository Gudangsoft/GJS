<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public int $unreadCount = 0;
    public bool $open = false;
    public array $notifications = [];

    public function mount(): void
    {
        if (Auth::check()) {
            try {
                $this->unreadCount = Auth::user()->unreadNotifications()->count();
            } catch (\Throwable) {
                $this->unreadCount = 0;
            }
        }
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;

        if ($this->open) {
            $this->loadNotifications();
        }
    }

    public function markRead(string $id): void
    {
        if (! Auth::check()) {
            return;
        }

        $notification = Auth::user()->notifications()->find($id);

        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
            $this->unreadCount = max(0, $this->unreadCount - 1);
        }

        $this->loadNotifications();
        $this->dispatch('notification-read');
    }

    public function markAllRead(): void
    {
        if (! Auth::check()) {
            return;
        }

        Auth::user()->unreadNotifications()->update(['read_at' => now()]);
        $this->unreadCount = 0;
        $this->loadNotifications();
        $this->dispatch('notification-read');
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }

    private function loadNotifications(): void
    {
        if (! Auth::check()) {
            $this->notifications = [];
            return;
        }

        try {
            $this->notifications = Auth::user()
                ->notifications()
                ->latest()
                ->limit(10)
                ->get()
                ->map(fn ($n) => [
                    'id'        => $n->id,
                    'data'      => $n->data,
                    'read_at'   => $n->read_at,
                    'read'      => ! is_null($n->read_at),
                    'time'      => $n->created_at->diffForHumans(),
                ])
                ->toArray();
        } catch (\Throwable) {
            $this->notifications = [];
        }
    }
}
