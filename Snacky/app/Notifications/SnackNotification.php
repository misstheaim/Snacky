<?php

namespace App\Notifications;

use App\Filament\Pages\CommentedSnacks;
use App\Filament\Resources\SnackResource;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SnackNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $notifycation;

    /**
     * Create a new notification instance.
     */
    public function __construct($notifycation)
    {
        $this->notifycation = $notifycation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $notifyLine = match ($this->notifycation->type) {
            'SUBMISSION' => 'Added new Snack by ' . $this->notifycation->snack->user->name,
            'ADDED_TO_THE_RECEIPT' => 'Congratulations! Your Snack has beed added to the receipt',
            'APPROVED' => 'Your Snack has been approved!',
            'REJECTED' => 'Your Snack has been rejected.',
            'COMMENTED' => 'Snacks, you have approved, has been commented ' . $this->notifycation->count . ' times',
            default => 'Unknown type'
        };

        return (new MailMessage())
            ->greeting("Hello, $notifiable->name")
            ->line('Snack related action.')
            ->line($notifyLine)
            ->action('View Snack', $this->notifycation->type === 'COMMENTED' ?
                CommentedSnacks::getUrl(['snacks' => $this->notifycation->snacks]) :
                SnackResource::getUrl('view', [$this->notifycation->snack->id]))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
