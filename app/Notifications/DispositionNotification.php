<?php

namespace App\Notifications;

use App\Models\Disposition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DispositionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * intance model disposition
     * @var \App\Models\Disposition $disposition
    */
    private $disposition;

    /**
     * Create a new notification instance.
     */
    public function __construct(Disposition $disposition)
    {
        $this->afterCommit();
        $this->disposition = $disposition;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'databases'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {

        $disposition = $this->disposition;
        $to = $disposition->employeeTo;
        $url = config('app.frontend_url')."/incoming-letters/dispositions/".$disposition->id;

        return (new MailMessage)
                    ->greeting('Hello, '.$to->user->full_name)
                    ->line('Sebuah surat telah di disposisikan kepada anda')
                    ->action('Lihat', $url)
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
            'message' => 'Disposisi surat masuk',
            'notifiable' => $notifiable
        ];
    }
}
