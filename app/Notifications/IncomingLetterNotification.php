<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\IncomingLetter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class IncomingLetterNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * instance of \App\Models\IncomingLetter
     * @var \App\Models\IncomingLetter $incomingLetter
    */
    private $incomingLetter;


    /**
     * Create a new notification instance.
     */
    public function __construct(IncomingLetter $incomingLetter)
    {
        $this->afterCommit();
        $this->incomingLetter = $incomingLetter;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $letter = $this->incomingLetter;
        $link = config('app.frontend_url').'/incoming-letters/show/'.$letter->id;
        $to = $letter->employee;
        return (new MailMessage)
                    ->greeting('Hello, '.$to->user->full_name)
                    ->subject('Informasi Surat Masuk')
                    ->line('Ada sebuah surat masuk yang ditjukan kepada anda, tekan tombol dibawah untuk informasi lebih lanjut')
                    ->action('Lihat', $link)
                    ->attach(asset(Storage::url($letter->file)))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $letter = $this->incomingLetter;
        return [
            'message' => 'Surat masuk yang ditujuakan kepada anda',
            'link' => config('app.frontend_url').'/incoming-letters/show/'.$letter->id,
        ];
    }
}
