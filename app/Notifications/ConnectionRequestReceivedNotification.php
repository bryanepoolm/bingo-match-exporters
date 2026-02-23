<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConnectionRequestReceivedNotification extends Notification
{
    use Queueable;

    public $match;
    public $senderName;

    /**
     * Create a new notification instance.
     */
    public function __construct($match, $senderName)
    {
        $this->match = $match;
        $this->senderName = $senderName;
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
            'type' => 'connection_request_received',
            'match_id' => $this->match->id,
            'message' => "You have received a new connection request from {$this->senderName}.",
            'url' => route('matches.show', $this->match->id),
        ];
    }
}
