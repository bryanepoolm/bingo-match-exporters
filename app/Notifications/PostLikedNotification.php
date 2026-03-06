<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostLikedNotification extends Notification implements \Illuminate\Contracts\Broadcasting\ShouldBroadcast
{
    use Queueable;

    public $post;
    public $likerName;

    /**
     * Create a new notification instance.
     */
    public function __construct($post, $likerName)
    {
        $this->post = $post;
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
            ->subject('A alguien le gustó tu publicación - Bingo Match')
            ->greeting('Hola,')
            ->line("A {$this->likerName} le ha gustado tu publicación.")
            ->action('Ver Publicación', $url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'post_liked',
            'post_id' => $this->post->id,
            'message' => "{$this->likerName} liked your post.",
            'url' => route('news.index'),
        ];
    }
}
