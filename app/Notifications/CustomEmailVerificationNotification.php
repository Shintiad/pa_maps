<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class CustomEmailVerificationNotification extends VerifyEmail
{
    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verifikasi Alamat Email Anda')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Terima kasih telah mendaftar di ' . config('app.name') . '.')
            ->line('Untuk mengaktifkan akun Anda, silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda:')
            ->action('Verifikasi Email', $verificationUrl)
            ->line('Link verifikasi ini akan kedaluwarsa dalam ' . Config::get('auth.verification.expire', 60) . ' menit.')
            ->line('Jika Anda tidak membuat akun, abaikan email ini.')
            ->salutation('Terima kasih,<br>' . config('app.name'))
            ->view('emails.verify-email', [
                'verificationUrl' => $verificationUrl,
                'user' => $notifiable
            ]);
    }
}