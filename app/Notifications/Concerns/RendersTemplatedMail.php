<?php

namespace App\Notifications\Concerns;

use App\Mail\TemplatedMail;
use App\Services\Email\EmailTemplateService;
use Illuminate\Notifications\Messages\MailMessage;

trait RendersTemplatedMail
{
    /**
     * @param  array<string, scalar|null>  $placeholders
     */
    protected function templatedMail(
        object $notifiable,
        string $category,
        array $placeholders = [],
        ?string $locale = null,
    ): MailMessage|TemplatedMail {
        $locale ??= data_get($notifiable, 'language') ?: config('email_templates.default_locale', 'en');
        $service = app(EmailTemplateService::class);
        $content = $service->mailContent($category, $placeholders, $locale);

        if ($content === null) {
            return (new MailMessage)
                ->subject((string) ($placeholders['subject'] ?? ucfirst(str_replace('_', ' ', $category))))
                ->line((string) ($placeholders['body'] ?? 'Notification from '.config('app.name')));
        }

        return new TemplatedMail(
            subjectLine: $content['subject'],
            htmlBody: (string) $content['html'],
            textBody: $content['text'],
            useBranding: $service->resolve($category, $locale)?->use_branding ?? true,
        );
    }

    /**
     * @param  list<string>  $preferenceChannels  Values like database, email
     * @return list<string>  Laravel channels: database, mail
     */
    protected function channelsFromPreferences(object $notifiable, string $notificationClass, array $preferenceChannels = ['database', 'email']): array
    {
        $preferences = app(\App\Services\UserNotificationPreferenceService::class);
        $channels = [];

        foreach ($preferenceChannels as $preferenceChannel) {
            if (! $preferences->isEnabled($notifiable, $notificationClass, $preferenceChannel)) {
                continue;
            }

            $channels[] = $preferenceChannel === 'email' ? 'mail' : $preferenceChannel;
        }

        return $channels;
    }
}
