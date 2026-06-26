<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class NotificationsPage extends Component
{
    use WithPagination;

    public function markAllRead(): void
    {
        if (Auth::check()) {
            Auth::user()->unreadNotifications()->update(['read_at' => now()]);
        }
    }

    public function markRead(string $id): void
    {
        if (! Auth::check()) {
            return;
        }

        $notification = Auth::user()->notifications()->find($id);
        $notification?->markAsRead();
    }

    public function render()
    {
        $notifications = Auth::user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('livewire.notifications-page', [
            'notifications' => $notifications,
        ])->title('Notifikasi');
    }
}
