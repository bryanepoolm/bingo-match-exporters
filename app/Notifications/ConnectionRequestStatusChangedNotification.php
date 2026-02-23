<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConnectionRequestStatusChangedNotification extends Notification
{
    use Queueable;

    public $match;
    public $changerName;
    public $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct($match, $changerName, $newStatus)
    {
        $this->match = $match;
        $this->changerName = $changerName;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'connection_request_status_changed',
            'match_id' => $this->match->id,
            'message' => "{$this->changerName} changed the status of your connection to {$this->newStatus}.",
            'url' => route('matches.show', $this->match->id),
        ];
    }
}
