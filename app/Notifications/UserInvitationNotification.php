<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Invitation $invitation,
        private readonly string $plainToken,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $frontendUrl = rtrim((string) config('app.frontend_url', config('app.url')), '/');
        $activationUrl = $frontendUrl.'/activation?token='.urlencode($this->plainToken);

        return (new MailMessage)
            ->subject('Activation de votre compte Revo')
            ->greeting('Bienvenue')
            ->line('Un administrateur vous a invité à activer votre compte de gestion de station-service.')
            ->line('Cette invitation expire le '.$this->invitation->expires_at->translatedFormat('d/m/Y à H:i').'.')
            ->action('Activer mon compte', $activationUrl)
            ->line('Si vous n’attendiez pas cette invitation, vous pouvez ignorer cet email.');
    }
}
