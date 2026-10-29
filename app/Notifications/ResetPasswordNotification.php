<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('Atur Ulang Kata Sandi - Panel Karang Taruna Demangan')
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun Panel Pengurus Dusun Demangan Anda.')
            ->action('Atur Ulang Kata Sandi', $url)
            ->line('Tautan atur ulang kata sandi ini berlaku selama 60 menit.')
            ->line('Jika Anda tidak merasa mengajukan permintaan ini, silakan abaikan email ini. Akun Anda tetap aman.')
            ->salutation("Salam hangat,\nPengurus Karang Taruna Dusun Demangan");
    }
}
