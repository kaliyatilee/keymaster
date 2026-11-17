<?php

namespace App\Domain\Identity\Notifications;

use App\Models\ParentalConsent;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ParentalConsentRequested extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Plain (not readonly) properties: queued notifications are serialized
     * and restored by SerializesModels.
     */
    public function __construct(
        public User $child,
        public ParentalConsent $consent,
        private string $token,
    ) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('consent.show', ['token' => $this->token]);

        return (new MailMessage)
            ->subject('Please approve your child\'s '.config('app.name').' account')
            ->greeting('Hello,')
            ->line("Someone using the name \"{$this->child->name}\" (username {$this->child->username}) has signed up for ".config('app.name').', a typing practice website, and gave this email address for a parent or guardian.')
            ->line('Until you approve, the account can only take practice tests that are not saved. We store their username, name, date of birth and typing results. You can ask us to delete the account at any time.')
            ->action('Review and respond', $url)
            ->line('This link expires on '.$this->consent->expires_at->toFormattedDayDateString().'. If you did not expect this email, you can ignore it and the account will stay inactive.');
    }
}
