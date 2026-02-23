<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConnectionRequestRejectedNotification extends Notification
{
    use Queueable;

    public $match;
    public $rejecterName;

    /**
     * Create a new notification instance.
     */
    public function __construct($match, $rejecterName)
    {
        $this->match = $match;
        $this->rejecterName = $rejecterName;
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
            'type' => 'connection_request_rejected',
            'match_id' => $this->match->id,
            'message' => "{$this->rejecterName} has rejected your connection request.",
            'url' => route('matches.show', $this->match->id),
        ];
    }
}
