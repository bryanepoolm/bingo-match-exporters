<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentLikedNotification extends Notification
{
    use Queueable;

    public $comment;
    public $likerName;

    /**
     * Create a new notification instance.
     */
    public function __construct($comment, $likerName)
    {
        $this->comment = $comment;
        $this->likerName = $likerName;
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
            'type' => 'comment_liked',
            'comment_id' => $this->comment->id,
            'message' => "{$this->likerName} liked your comment.",
            'url' => route('news.index'),
        ];
    }
}
