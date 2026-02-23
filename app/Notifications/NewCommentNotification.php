<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
    use Queueable;

    public $comment;
    public $commenterName;
    public $post;

    /**
     * Create a new notification instance.
     */
    public function __construct($comment, $commenterName, $post)
    {
        $this->comment = $comment;
        $this->commenterName = $commenterName;
        $this->post = $post;
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
            'type' => 'new_comment',
            'post_id' => $this->post->id,
            'comment_id' => $this->comment->id,
            'message' => "{$this->commenterName} commented on your post.",
            'url' => route('news.index'),
        ];
    }
}
