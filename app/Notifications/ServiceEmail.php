<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceEmail extends Notification
{
    public function __construct(private array $content) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)->subject($this->content['subject'])->greeting('Hello '.$this->content['name'].',');
        foreach ($this->content['lines'] as $line) {
            $mail->line($line);
        }
        if (! empty($this->content['url'])) {
            $mail->action($this->content['action'] ?? 'Continue', $this->content['url']);
        }

        return $mail;
    }
}
