<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentLikedNotification extends Notification implements \Illuminate\Contracts\Broadcasting\ShouldBroadcast
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
        return ['database', 'mail', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        $url = route('news.index');

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('A alguien le gustó tu comentario - Bingo Match')
            ->greeting('Hola,')
            ->line("A {$this->likerName} le ha gustado tu comentario.")
            ->action('Ver Comentario', $url);
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
