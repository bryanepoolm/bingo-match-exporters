<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConnectionRequestAcceptedNotification extends Notification
{
    use Queueable;

    public $match;
    public $accepterName;

    /**
     * Create a new notification instance.
     */
    public function __construct($match, $accepterName)
    {
        $this->match = $match;
        $this->accepterName = $accepterName;
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
            'type' => 'connection_request_accepted',
            'match_id' => $this->match->id,
            'message' => "{$this->accepterName} has accepted your connection request.",
            'url' => route('matches.show', $this->match->id), // Link to workspace if accepted
        ];
    }
}
