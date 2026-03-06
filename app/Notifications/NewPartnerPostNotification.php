<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPartnerPostNotification extends Notification implements \Illuminate\Contracts\Broadcasting\ShouldBroadcast
{
    use Queueable;

    public $post;
    public $partnerName;
    public $titleSnippet;

    /**
     * Create a new notification instance.
     */
    public function __construct($post, $partnerName, $titleSnippet)
    {
        $this->post = $post;
        $this->partnerName = $partnerName;
        $this->titleSnippet = $titleSnippet;
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
            ->subject('Nueva publicación de un socio - Bingo Match')
            ->greeting('Hola,')
            ->line("{$this->partnerName} ha creado una nueva publicación:")
            ->line("\"{$this->titleSnippet}\"")
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
            'type' => 'new_partner_post',
            'post_id' => $this->post->id,
            'message' => "{$this->partnerName} created a new post: \"{$this->titleSnippet}\"",
            'url' => route('news.index'),
        ];
    }
}
