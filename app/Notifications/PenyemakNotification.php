<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PenyemakNotification extends Notification
{
    use Queueable;

    public $applicant;

    public function __construct($applicant)
    {
        $this->applicant = $applicant;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $statusUrl = url('/status/' . $this->applicant->id);

        return (new MailMessage)
            ->subject('Tindakan Permohonan Tadika Diperlukan')
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('Permohonan baru atau kemaskini memerlukan semakan anda.')
            ->line('Nama Pemohon: ' . $this->applicant->nama)
            ->line('Status: ' . $this->applicant->status)
            ->action('Semak Permohonan', $statusUrl)
            ->line('Terima kasih.');
    }
}
          
 
