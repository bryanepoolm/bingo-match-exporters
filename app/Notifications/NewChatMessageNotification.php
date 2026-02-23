<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewChatMessageNotification extends Notification
{
    use Queueable;

    public $match;
    public $senderName;
    public $snippet;

    /**
     * Create a new notification instance.
     */
    public function __construct($match, $senderName, $snippet)
    {
        $this->match = $match;
        $this->senderName = $senderName;
        $this->snippet = $snippet;
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
            'type' => 'new_chat_message',
            'match_id' => $this->match->id,
            'message' => "New message from {$this->senderName}: \"{$this->snippet}\"",
            'url' => route('matches.show', $this->match->id),
        ];
    }
}
