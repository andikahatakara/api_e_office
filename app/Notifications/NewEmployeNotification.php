<?php

namespace App\Notifications;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewEmployeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var string $password
    */
    private $password;

    /**
     * Intance of model Employee
     * @var \App\Models\Employee $employee
    */
    private $employee;

    /**
     * Create a new notification instance.
     */
    public function __construct(Employee $employee, string $password)
    {
        $this->afterCommit();
        $this->employee = $employee;
        $this->password = $password;
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

        $password = $this->password;
        $user = $this->employee->user;
        $url = config('app.frontend_url').'/login';

        $login = "email : **$user->email**, password : **$password**";

        return (new MailMessage)
                    ->greeting('Hallo, **'.$user->full_name.'**')
                    ->subject('Pengguna Baru '.config('app.name'))
                    ->line('Selamat datang, anda telah ditambahakan menjadi pengguna pada aplikasi **'. config('app.name') .'** ')
                    ->line('Berikut adalah informasi untuk login anda')
                    ->line($login)
                    ->action('Login', $url)
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
