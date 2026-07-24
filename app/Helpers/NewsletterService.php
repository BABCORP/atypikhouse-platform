<?php

namespace App\Helpers;

use App\Models\ContactMessage;

final class NewsletterService
{
    public function subscribe(string $email, ?int $userId = null): string
    {
        $message = [
            'name' => 'Newsletter',
            'email' => $email,
            'subject' => 'Newsletter',
            'message' => 'Inscription newsletter enregistrée dans le cadre du projet étudiant. Aucun email réel ne sera envoyé.',
        ];

        (new ContactMessage())->create($message);

        if (!config('brevo_enabled') || config('brevo_list_id') === '') {
            audit($userId, 'newsletter_demo_subscribe', 'contact_message');
            return 'demo';
        }

        // Structure Brevo prête pour intégration future. Aucun appel réseau n'est lancé en MVP académique.
        audit($userId, 'newsletter_brevo_ready_subscribe', 'contact_message');
        return 'brevo_ready';
    }
}
