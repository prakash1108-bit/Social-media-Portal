<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;

class FriendRequestReceived extends Notification implements ShouldQueue
{
    use Queueable;

    protected $sender;

    public function __construct(User $sender)
    {
        $this->sender = $sender;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->sender->name . ' has sent you a friend request.')
            ->action('View Friend Requests', url('/friends/requests'))
            ->line('Thank you for using our app!');
    }
}
