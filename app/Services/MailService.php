<?php

namespace App\Services;

use Throwable;

final class MailService
{
    private array $config;
    private string $lastSmtpError = '';

    public function __construct()
    {
        $this->config = require dirname(__DIR__, 2) . '/config/mail.php';
    }

    public function send(string $to, string $subject, string $htmlBody, ?string $textBody = null, string $event = 'transactional'): bool
    {
        $to = strtolower(trim($to));
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->logSafe('mail_send_failed', $to, $subject, $event, 'Adresse destinataire invalide.');
            return false;
        }

        $textBody ??= trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $htmlBody)));
        $htmlBody = $this->renderEmail($subject, $htmlBody);
        if ($this->shouldLogOnly()) {
            $this->logDemo($to, $subject, $event, $textBody);
            return true;
        }

        if (!$this->smtpReady()) {
            $this->logSafe('mail_send_failed', $to, $subject, $event, 'Configuration SMTP incomplète.');
            return false;
        }

        try {
            $this->lastSmtpError = '';
            $sent = $this->sendViaSmtp($to, $subject, $htmlBody, $textBody);
            if (!$sent && $this->brevoApiReady()) {
                $smtpError = $this->lastSmtpError;
                $sent = $this->sendViaBrevoApi($to, $subject, $htmlBody, $textBody);
                if (!$sent && $smtpError !== '') {
                    $this->lastSmtpError = $smtpError . ' Fallback API Brevo échoué : ' . $this->lastSmtpError;
                }
            }
            $this->logSafe($sent ? 'mail_send_success' : 'mail_send_failed', $to, $subject, $event, $sent ? 'Email envoyé.' : 'Échec SMTP : ' . ($this->lastSmtpError ?: 'raison inconnue.'));
            return $sent;
        } catch (Throwable $exception) {
            $this->logSafe('mail_send_failed', $to, $subject, $event, 'Erreur SMTP : ' . $this->safeErrorMessage($exception->getMessage()));
            return false;
        }
    }

    public function sendContactNotification(string $senderName, string $senderEmail, string $subject): bool
    {
        return $this->sendInternalNotification(
            'Nouveau message reçu depuis AtypikHouse',
            'Un nouveau message a été envoyé depuis le formulaire de contact.',
            'contact_admin_notification',
            [
                'Nom' => $senderName,
                'Email' => $senderEmail,
                'Sujet' => $subject,
            ]
        );
    }

    public function sendAccountPendingNotification(string $to, string $firstName, string $role): bool
    {
        $roleLabel = $role === 'owner' ? 'propriétaire' : 'locataire';
        $userSent = $this->send(
            $to,
            'Votre compte AtypikHouse est en cours d’examen',
            '<p>Bonjour ' . e($firstName) . ',</p>'
            . '<p>Votre compte AtypikHouse a bien été créé.</p>'
            . '<p>Pour garantir la sécurité et la qualité de la plateforme, il est actuellement en cours d’examen par un administrateur.</p>'
            . '<p>Vous recevrez un nouvel email dès que votre compte aura été validé ou refusé.</p>'
            . '<p>À bientôt,<br>L’équipe AtypikHouse</p>'
            . '<p><strong>Projet étudiant fictif.</strong> Aucun achat, paiement ou réservation réelle ne peut être effectué.</p>',
            null,
            'account_pending_user'
        );
        $this->sendInternalNotification(
            'Nouveau compte à valider sur AtypikHouse',
            'Un nouveau compte vient d’être créé et attend une validation administrateur.',
            'account_pending_admin',
            [
                'Prénom' => $firstName,
                'Email' => $to,
                'Rôle demandé' => $roleLabel,
                'Lien back-office' => $this->appUrl('/admin/utilisateurs'),
            ]
        );

        return $userSent;
    }

    public function sendAccountApprovedNotification(string $to, string $firstName): bool
    {
        $loginUrl = $this->appUrl('/connexion');
        return $this->send(
            $to,
            'Bienvenue sur AtypikHouse, votre compte est validé',
            '<p>Bonjour ' . e($this->firstName($firstName)) . ',</p>'
            . '<p>Bonne nouvelle, votre compte AtypikHouse a été validé par notre équipe.</p>'
            . '<p>Vous pouvez maintenant vous connecter et accéder à votre espace personnel.</p>'
            . $this->emailButton('Se connecter à mon espace', $loginUrl)
            . '<p style="font-size:14px;line-height:1.6;color:#6B7280;">Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br><a href="' . e($loginUrl) . '" style="color:#2F5D50;">' . e($loginUrl) . '</a></p>'
            . '<p>Bienvenue sur AtypikHouse,<br>L’équipe AtypikHouse</p>'
            . '<p><strong>Projet étudiant fictif.</strong> Aucun achat, paiement ou réservation réelle ne peut être effectué.</p>',
            null,
            'account_approved'
        );
    }

    public function sendAccountRejectedNotification(string $to, string $firstName): bool
    {
        return $this->send(
            $to,
            'Votre compte AtypikHouse n’a pas été validé',
            '<p>Bonjour ' . e($firstName) . ',</p>'
            . '<p>Après vérification, votre compte AtypikHouse n’a pas été validé.</p>'
            . '<p>Pour plus d’informations, vous pouvez contacter l’équipe AtypikHouse à l’adresse ' . e((string) $this->config['support_email']) . '.</p>'
            . '<p>L’équipe AtypikHouse</p>',
            null,
            'account_rejected'
        );
    }

    public function sendAccountSuspendedNotification(string $to, string $firstName): bool
    {
        return $this->send(
            $to,
            'Votre compte AtypikHouse a été suspendu',
            '<p>Bonjour ' . e($firstName) . ',</p>'
            . '<p>Votre compte AtypikHouse a été temporairement suspendu.</p>'
            . '<p>Pour plus d’informations, vous pouvez contacter l’équipe AtypikHouse à l’adresse ' . e((string) $this->config['support_email']) . '.</p>'
            . '<p>L’équipe AtypikHouse</p>',
            null,
            'account_suspended'
        );
    }

    public function sendTestEmail(string $to): bool
    {
        return $this->send(
            $to,
            'Test SMTP AtypikHouse',
            '<p>Ceci est un email de test envoyé depuis Render par AtypikHouse.</p>',
            'Ceci est un email de test envoyé depuis Render par AtypikHouse.',
            'smtp_test'
        );
    }

    public function testRecipient(): string
    {
        $username = trim((string) $this->config['smtp']['username']);
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            return $username;
        }

        return (string) $this->config['admin_email'];
    }

    public function diagnostics(): array
    {
        return [
            'smtp_enabled' => (bool) $this->config['enabled'],
            'real_email_sending' => (bool) $this->config['real_email_sending'],
            'demo_mode' => (bool) $this->config['demo_mode'],
            'log_only' => (bool) $this->config['log_only'],
            'mail_host_defined' => trim((string) $this->config['smtp']['host']) !== '',
            'mail_username_defined' => trim((string) $this->config['smtp']['username']) !== '',
            'mail_password_defined' => trim((string) $this->config['smtp']['password']) !== '',
            'mail_from_defined' => filter_var((string) $this->config['from']['email'], FILTER_VALIDATE_EMAIL) !== false,
            'admin_email_defined' => filter_var((string) $this->config['admin_email'], FILTER_VALIDATE_EMAIL) !== false,
            'brevo_api_key_defined' => $this->brevoApiReady(),
            'smtp_host' => (string) $this->config['smtp']['host'],
            'smtp_port' => (int) $this->config['smtp']['port'],
            'smtp_encryption' => (string) $this->config['smtp']['encryption'],
            'smtp_username' => (string) $this->config['smtp']['username'],
        ];
    }

    public function sendPropertySubmittedNotification(string $ownerEmail, string $propertyTitle, string $ownerFirstName = '', array $details = []): bool
    {
        $ownerSent = $this->send(
            $ownerEmail,
            'Votre logement a été soumis à validation',
            '<p>Bonjour ' . e($ownerFirstName !== '' ? $ownerFirstName : 'propriétaire') . ',</p>'
            . '<p>Votre logement "' . e($propertyTitle) . '" a bien été soumis à l’équipe AtypikHouse.</p>'
            . '<p>Il sera examiné par un administrateur avant sa publication.</p>'
            . '<p>Vous recevrez un email dès qu’une décision aura été prise.</p>'
            . '<p>L’équipe AtypikHouse</p>',
            null,
            'property_submitted_owner'
        );
        $this->sendInternalNotification(
            'Nouveau logement à valider',
            'Un propriétaire a soumis un logement à validation.',
            'property_submitted_admin',
            [
                'Logement' => $propertyTitle,
                'Propriétaire' => $ownerEmail,
                'Ville' => $details['city'] ?? '',
                'Prix par nuit' => isset($details['price_per_night']) ? money((float) $details['price_per_night']) : '',
                'Lien admin' => $this->appUrl('/admin/logements'),
            ]
        );
        return $ownerSent;
    }

    public function sendPropertyApprovedNotification(string $ownerEmail, string $propertyTitle, string $ownerFirstName = '', string $slug = ''): bool
    {
        $link = $slug !== '' ? $this->appUrl('/hebergements/' . $slug) : $this->appUrl('/hebergements');
        return $this->send(
            $ownerEmail,
            'Votre logement AtypikHouse a été validé',
            '<p>Bonjour ' . e($ownerFirstName !== '' ? $ownerFirstName : 'propriétaire') . ',</p>'
            . '<p>Votre logement "' . e($propertyTitle) . '" a été validé par l’administrateur.</p>'
            . '<p>Il est maintenant visible sur la plateforme AtypikHouse.</p>'
            . $this->emailButton('Voir mon logement', $link)
            . '<p>L’équipe AtypikHouse</p>',
            null,
            'property_approved'
        );
    }

    public function sendPropertyRejectedNotification(string $ownerEmail, string $propertyTitle, string $ownerFirstName = '', string $reason = ''): bool
    {
        $reasonBlock = $reason !== '' ? '<p><strong>Motif :</strong><br>' . e($reason) . '</p>' : '';
        return $this->send(
            $ownerEmail,
            'Votre logement AtypikHouse n’a pas été validé',
            '<p>Bonjour ' . e($ownerFirstName !== '' ? $ownerFirstName : 'propriétaire') . ',</p>'
            . '<p>Après examen, votre logement "' . e($propertyTitle) . '" n’a pas été validé.</p>'
            . $reasonBlock
            . '<p>Vous pouvez modifier votre annonce puis la soumettre à nouveau.</p>'
            . '<p>L’équipe AtypikHouse</p>',
            null,
            'property_rejected'
        );
    }

    public function sendPropertyChangeSubmittedNotification(string $ownerEmail, string $ownerFirstName, string $propertyTitle): bool
    {
        $ownerSent = $this->send(
            $ownerEmail,
            'Votre demande de modification est en cours d’examen',
            '<p>Bonjour ' . e($ownerFirstName !== '' ? $ownerFirstName : 'propriétaire') . ',</p>'
            . '<p>Votre demande de modification pour le logement "' . e($propertyTitle) . '" a bien été transmise.</p>'
            . '<p>Elle doit être examinée par un administrateur avant d’être publiée.</p>'
            . '<p>L’équipe AtypikHouse</p>',
            null,
            'property_change_submitted_owner'
        );
        $this->sendInternalNotification(
            'Modification de logement à valider',
            'Un propriétaire a demandé une modification sur un logement.',
            'property_change_submitted_admin',
            [
                'Logement' => $propertyTitle,
                'Propriétaire' => $ownerEmail,
                'Lien admin' => $this->appUrl('/admin/logements/modifications'),
            ]
        );
        return $ownerSent;
    }

    public function sendPropertyChangeApprovedNotification(string $ownerEmail, string $ownerFirstName, string $propertyTitle): bool
    {
        return $this->send(
            $ownerEmail,
            'Votre modification de logement a été validée',
            '<p>Bonjour ' . e($ownerFirstName !== '' ? $ownerFirstName : 'propriétaire') . ',</p>'
            . '<p>Votre demande de modification pour le logement "' . e($propertyTitle) . '" a été validée.</p>'
            . '<p>Les nouvelles informations sont maintenant visibles sur la plateforme.</p>'
            . '<p>L’équipe AtypikHouse</p>',
            null,
            'property_change_approved'
        );
    }

    public function sendPropertyChangeRejectedNotification(string $ownerEmail, string $ownerFirstName, string $propertyTitle, string $reason = ''): bool
    {
        $reasonBlock = $reason !== '' ? '<p><strong>Motif :</strong><br>' . e($reason) . '</p>' : '';
        return $this->send(
            $ownerEmail,
            'Votre modification de logement n’a pas été validée',
            '<p>Bonjour ' . e($ownerFirstName !== '' ? $ownerFirstName : 'propriétaire') . ',</p>'
            . '<p>Après examen, votre demande de modification pour le logement "' . e($propertyTitle) . '" n’a pas été validée.</p>'
            . $reasonBlock
            . '<p>Vous pouvez corriger les informations puis soumettre une nouvelle demande.</p>'
            . '<p>L’équipe AtypikHouse</p>',
            null,
            'property_change_rejected'
        );
    }

    public function sendBookingPendingNotification(string $tenantEmail, string $propertyTitle, array $booking = []): bool
    {
        $dates = $this->bookingDates($booking);
        $tenantSent = $this->send(
            $tenantEmail,
            'Votre demande de réservation a bien été reçue',
            '<p>Bonjour ' . e((string) ($booking['tenant_first_name'] ?? '')) . ',</p>'
            . '<p>Votre demande de réservation pour "' . e($propertyTitle) . '" a bien été enregistrée.</p>'
            . '<p>Elle est maintenant en attente de validation par l’administrateur.</p>'
            . '<ul>'
            . '<li><strong>Logement :</strong> ' . e($propertyTitle) . '</li>'
            . '<li><strong>Dates :</strong> ' . e($dates) . '</li>'
            . '<li><strong>Nombre de voyageurs :</strong> ' . e((string) ($booking['guests_count'] ?? '')) . '</li>'
            . '<li><strong>Total :</strong> ' . (isset($booking['total_price']) ? e(money((float) $booking['total_price'])) : '') . '</li>'
            . '<li><strong>Statut :</strong> En attente de validation</li>'
            . '</ul>'
            . '<p>L’équipe AtypikHouse</p>',
            null,
            'booking_pending_tenant'
        );
        $this->sendInternalNotification(
            'Nouvelle réservation à valider',
            'Une nouvelle demande de réservation attend une validation administrateur.',
            'booking_pending_admin',
            [
                'Locataire' => $tenantEmail,
                'Logement' => $propertyTitle,
                'Propriétaire' => $booking['owner_email'] ?? '',
                'Dates' => $dates,
                'Total' => isset($booking['total_price']) ? money((float) $booking['total_price']) : '',
                'Lien admin' => $this->appUrl('/admin/reservations'),
            ]
        );
        return $tenantSent;
    }

    public function sendBookingAwaitingPaymentNotification(array $booking): bool
    {
        $propertyTitle = (string) $booking['title'];
        $tenantSent = $this->send(
            (string) $booking['tenant_email'],
            'Votre réservation est validée, paiement fictif requis',
            '<p>Bonjour ' . e((string) ($booking['tenant_first_name'] ?? '')) . ',</p>'
            . '<p>Votre réservation pour "' . e($propertyTitle) . '" a été validée par l’administrateur.</p>'
            . '<p>Pour finaliser cette réservation, vous devez maintenant effectuer le paiement fictif depuis votre espace locataire.</p>'
            . $this->emailButton('Voir ma réservation', $this->appUrl('/locataire/reservations'))
            . '<p><strong>Rappel :</strong> ce paiement est fictif et réalisé uniquement dans le cadre d’une démonstration académique.</p>'
            . '<p>L’équipe AtypikHouse</p>',
            null,
            'booking_payment_required_tenant'
        );
        $ownerSent = $this->send(
            (string) $booking['owner_email'],
            'Une réservation de votre logement est en attente de paiement',
            '<p>Bonjour ' . e((string) ($booking['owner_first_name'] ?? '')) . ',</p>'
            . '<p>Une réservation pour votre logement "' . e($propertyTitle) . '" a été validée par l’administrateur.</p>'
            . '<p>Elle est maintenant en attente du paiement fictif du locataire.</p>'
            . '<p>L’équipe AtypikHouse</p>',
            null,
            'booking_payment_required_owner'
        );
        return $tenantSent && $ownerSent;
    }

    public function sendBookingConfirmedNotification(string $tenantEmail, string $ownerEmail, string $propertyTitle): bool
    {
        $tenantSent = $this->send(
            $tenantEmail,
            'Votre réservation fictive a été confirmée',
            '<p>Votre réservation fictive pour "' . e($propertyTitle) . '" a été confirmée.</p>',
            null,
            'booking_confirmed_tenant'
        );
        $ownerSent = $this->send(
            $ownerEmail,
            'Une réservation a été confirmée pour votre logement',
            '<p>Une réservation fictive a été confirmée pour "' . e($propertyTitle) . '".</p>',
            null,
            'booking_confirmed_owner'
        );
        return $tenantSent && $ownerSent;
    }

    public function sendFakePaymentConfirmedNotification(array $booking): bool
    {
        $propertyTitle = (string) $booking['title'];
        $dates = $this->bookingDates($booking);
        $tenantSent = $this->send(
            (string) $booking['tenant_email'],
            'Votre réservation AtypikHouse est confirmée',
            '<p>Bonjour ' . e((string) ($booking['tenant_first_name'] ?? '')) . ',</p>'
            . '<p>Votre paiement fictif a été validé.</p>'
            . '<p>Votre réservation pour "' . e($propertyTitle) . '" est maintenant confirmée.</p>'
            . '<ul>'
            . '<li><strong>Dates :</strong> ' . e($dates) . '</li>'
            . '<li><strong>Total :</strong> ' . e(money((float) $booking['total_price'])) . '</li>'
            . '<li><strong>Référence fictive :</strong> ' . e((string) ($booking['test_transaction_id'] ?? '')) . '</li>'
            . '</ul>'
            . '<p>Aucun montant réel n’a été débité.</p>'
            . '<p>L’équipe AtypikHouse</p>',
            null,
            'fake_payment_confirmed_tenant'
        );
        $ownerSent = $this->send(
            (string) $booking['owner_email'],
            'Une réservation de votre logement est confirmée',
            '<p>Bonjour ' . e((string) ($booking['owner_first_name'] ?? '')) . ',</p>'
            . '<p>La réservation pour votre logement "' . e($propertyTitle) . '" est maintenant confirmée.</p>'
            . '<p>Le paiement fictif a été validé par le locataire.</p>'
            . '<p>L’équipe AtypikHouse</p>',
            null,
            'fake_payment_confirmed_owner'
        );
        return $tenantSent && $ownerSent;
    }

    public function sendBookingCancelledNotification(array $booking, string $reason = ''): bool
    {
        $propertyTitle = (string) $booking['title'];
        $dates = $this->bookingDates($booking);
        $reasonBlock = $reason !== '' ? '<p><strong>Motif :</strong><br>' . e($reason) . '</p>' : '';
        $tenantSent = $this->send(
            (string) $booking['tenant_email'],
            'Votre réservation AtypikHouse a été annulée',
            '<p>Bonjour ' . e((string) ($booking['tenant_first_name'] ?? '')) . ',</p>'
            . '<p>Votre réservation pour "' . e($propertyTitle) . '" a été annulée.</p>'
            . '<ul><li><strong>Dates :</strong> ' . e($dates) . '</li><li><strong>Statut :</strong> Annulée</li></ul>'
            . $reasonBlock
            . '<p>Aucun paiement réel n’a été effectué dans le cadre de ce projet étudiant fictif.</p>'
            . '<p>L’équipe AtypikHouse</p>',
            null,
            'booking_cancelled_tenant'
        );
        $ownerSent = $this->send(
            (string) $booking['owner_email'],
            'Une réservation de votre logement a été annulée',
            '<p>Bonjour ' . e((string) ($booking['owner_first_name'] ?? '')) . ',</p>'
            . '<p>La réservation concernant votre logement "' . e($propertyTitle) . '" a été annulée.</p>'
            . '<ul><li><strong>Locataire :</strong> ' . e((string) ($booking['tenant_email'] ?? '')) . '</li><li><strong>Dates :</strong> ' . e($dates) . '</li></ul>'
            . $reasonBlock
            . '<p>L’équipe AtypikHouse</p>',
            null,
            'booking_cancelled_owner'
        );
        return $tenantSent && $ownerSent;
    }

    public function sendPasswordResetDemo(string $to, string $resetUrl): bool
    {
        return $this->send(
            $to,
            'Lien de réinitialisation de démonstration AtypikHouse',
            '<p>Un lien de réinitialisation de démonstration a été généré :</p><p><a href="' . e($resetUrl) . '">' . e($resetUrl) . '</a></p>',
            'Lien de réinitialisation de démonstration : ' . $resetUrl,
            'password_reset_demo'
        );
    }

    public function sendInternalNotification(string $subject, string $message, string $event, array $context = []): bool
    {
        $lines = '<p>' . e($message) . '</p>';
        if ($context !== []) {
            $lines .= '<ul>';
            foreach ($context as $label => $value) {
                $lines .= '<li><strong>' . e((string) $label) . ' :</strong> ' . e((string) $value) . '</li>';
            }
            $lines .= '</ul>';
        }
        return $this->send((string) $this->config['admin_email'], $subject, $lines, null, $event);
    }

    public function logInternalNotification(string $subject, string $message, string $event, array $context = []): bool
    {
        $this->writeLog([
            'date' => date('c'),
            'mode' => 'internal_notification_logged',
            'event' => $event,
            'recipient' => (string) $this->config['admin_email'],
            'subject' => $subject,
            'message' => $message,
            'context' => $context,
        ]);

        if (function_exists('audit')) {
            audit(null, 'internal_notification_logged', 'mail', null);
        }

        return true;
    }

    private function bookingDates(array $booking): string
    {
        $start = (string) ($booking['start_date'] ?? '');
        $end = (string) ($booking['end_date'] ?? '');
        return trim($start . ($end !== '' ? ' au ' . $end : ''));
    }

    private function appUrl(string $path): string
    {
        if (function_exists('app_url')) {
            return app_url($path);
        }

        $base = rtrim((string) ($this->config['app_url'] ?? ''), '/');
        $isPlaceholder = $base === ''
            || str_contains($base, 'ton-url-render')
            || str_contains($base, 'CHANGE_ME')
            || str_contains($base, 'URL_RENDER')
            || str_contains($base, 'ton-site')
            || str_contains($base, 'atypikhouse.test');

        if ($isPlaceholder) {
            $host = $_SERVER['HTTP_HOST'] ?? '';
            if ($host !== '') {
                $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https' ? 'https' : 'http';
                $base = $scheme . '://' . $host;
            } else {
                $base = 'https://atypikhouse-platform.onrender.com';
            }
        }

        return $base . '/' . ltrim($path, '/');
    }

    private function firstName(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    private function emailButton(string $label, string $url): string
    {
        return '<p style="text-align:center;margin:28px 0;">'
            . '<a href="' . e($url) . '" style="display:inline-block;background:#C96F4A;color:#ffffff;text-decoration:none;padding:14px 22px;border-radius:999px;font-weight:bold;font-size:15px;">'
            . e($label)
            . '</a></p>';
    }

    private function renderEmail(string $subject, string $content): string
    {
        return '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>' . e($subject) . '</title></head>'
            . '<body style="margin:0;padding:0;background:#F6F1E8;font-family:Arial,Helvetica,sans-serif;color:#24332E;">'
            . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F6F1E8;padding:32px 12px;">'
            . '<tr><td align="center">'
            . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#FFFDF8;border-radius:18px;overflow:hidden;border:1px solid #E8DDCF;">'
            . '<tr><td style="padding:28px 32px;background:#2F5D50;text-align:center;">'
            . '<div style="font-size:30px;line-height:1.1;font-weight:700;color:#FFFDF8;font-family:Georgia,Times,serif;">AtypikHouse</div>'
            . '<div style="margin-top:8px;font-size:13px;letter-spacing:0.08em;text-transform:uppercase;color:#DCECE4;">Séjours insolites et nature</div>'
            . '</td></tr>'
            . '<tr><td style="padding:32px;">'
            . '<h1 style="margin:0 0 18px;color:#2F5D50;font-size:26px;line-height:1.25;font-family:Georgia,Times,serif;">' . e($subject) . '</h1>'
            . '<div style="font-size:16px;line-height:1.65;color:#24332E;">' . $content . '</div>'
            . '</td></tr>'
            . '<tr><td style="padding:20px 32px;background:#F6F1E8;color:#6B7280;font-size:13px;line-height:1.5;text-align:center;">'
            . '<strong style="color:#2F5D50;">AtypikHouse</strong><br>'
            . 'Projet étudiant fictif. Aucun achat, paiement ou réservation réelle ne peut être effectué.'
            . '</td></tr>'
            . '</table>'
            . '</td></tr></table>'
            . '</body></html>';
    }

    private function shouldLogOnly(): bool
    {
        return (bool) $this->config['demo_mode'] || (bool) $this->config['log_only'];
    }

    private function smtpReady(): bool
    {
        return ((bool) $this->config['enabled'] || (bool) $this->config['real_email_sending'])
            && trim((string) $this->config['smtp']['host']) !== ''
            && trim((string) $this->config['smtp']['username']) !== ''
            && trim((string) $this->config['smtp']['password']) !== ''
            && filter_var((string) $this->config['from']['email'], FILTER_VALIDATE_EMAIL);
    }

    private function brevoApiReady(): bool
    {
        return trim((string) ($this->config['brevo_api_key'] ?? '')) !== ''
            && filter_var((string) $this->config['from']['email'], FILTER_VALIDATE_EMAIL);
    }

    private function sendViaSmtp(string $to, string $subject, string $htmlBody, string $textBody): bool
    {
        $host = (string) $this->config['smtp']['host'];
        $port = (int) $this->config['smtp']['port'];
        $encryption = strtolower((string) $this->config['smtp']['encryption']);
        $remote = $encryption === 'ssl' ? 'ssl://' . $host . ':' . $port : $host . ':' . $port;
        $socket = stream_socket_client($remote, $errno, $errstr, 15, STREAM_CLIENT_CONNECT);
        if (!$socket) {
            $this->lastSmtpError = 'Connexion impossible à ' . $host . ':' . $port . ' (' . $errno . ' ' . $this->safeErrorMessage((string) $errstr) . ')';
            return false;
        }

        $read = static fn (): string => (string) fgets($socket, 515);
        $write = static function (string $command) use ($socket): void {
            fwrite($socket, $command . "\r\n");
        };
        $expect = function (array $codes, string $step) use ($read): bool {
            $response = $read();
            $ok = in_array(substr($response, 0, 3), $codes, true);
            if (!$ok) {
                $this->lastSmtpError = $step . ' refusé (' . $this->safeErrorMessage(trim($response) ?: 'réponse vide') . ')';
            }
            return $ok;
        };

        if (!$expect(['220'], 'Connexion SMTP')) {
            fclose($socket);
            return false;
        }
        $write('EHLO atypikhouse.local');
        while (($line = $read()) !== '' && isset($line[3]) && $line[3] === '-') {
            // consume multiline response
        }
        if ($encryption === 'tls') {
            $write('STARTTLS');
            if (!$expect(['220'], 'STARTTLS')) {
                fclose($socket);
                return false;
            }
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                $this->lastSmtpError = 'Activation TLS impossible.';
                fclose($socket);
                return false;
            }
            $write('EHLO atypikhouse.local');
            while (($line = $read()) !== '' && isset($line[3]) && $line[3] === '-') {
                // consume multiline response
            }
        }
        $write('AUTH LOGIN');
        if (!$expect(['334'], 'AUTH LOGIN')) {
            fclose($socket);
            return false;
        }
        $write(base64_encode((string) $this->config['smtp']['username']));
        if (!$expect(['334'], 'Identifiant SMTP')) {
            fclose($socket);
            return false;
        }
        $write(base64_encode((string) $this->config['smtp']['password']));
        if (!$expect(['235'], 'Mot de passe SMTP')) {
            fclose($socket);
            return false;
        }

        $from = (string) $this->config['from']['email'];
        $boundary = '=_AtypikHouse_' . bin2hex(random_bytes(8));
        $headers = [
            'From: ' . $this->headerAddress((string) $this->config['from']['name'], $from),
            'To: <' . $to . '>',
            'Subject: ' . $this->encodeHeader($subject),
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        ];
        $message = implode("\r\n", $headers) . "\r\n\r\n"
            . '--' . $boundary . "\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\n" . $textBody . "\r\n"
            . '--' . $boundary . "\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n" . $htmlBody . "\r\n"
            . '--' . $boundary . "--\r\n";

        $write('MAIL FROM:<' . $from . '>');
        $ok = $expect(['250'], 'MAIL FROM');
        $write('RCPT TO:<' . $to . '>');
        $ok = $ok && $expect(['250', '251'], 'RCPT TO');
        $write('DATA');
        $ok = $ok && $expect(['354'], 'DATA');
        $write($message . "\r\n.");
        $ok = $ok && $expect(['250'], 'Envoi du message');
        $write('QUIT');
        fclose($socket);
        return $ok;
    }

    private function sendViaBrevoApi(string $to, string $subject, string $htmlBody, string $textBody): bool
    {
        $payload = json_encode([
            'sender' => [
                'name' => (string) $this->config['from']['name'],
                'email' => (string) $this->config['from']['email'],
            ],
            'to' => [['email' => $to]],
            'subject' => $subject,
            'htmlContent' => $htmlBody,
            'textContent' => $textBody,
        ], JSON_UNESCAPED_UNICODE);

        if ($payload === false) {
            $this->lastSmtpError = 'Payload API Brevo invalide.';
            return false;
        }

        $headers = [
            'accept: application/json',
            'content-type: application/json',
            'api-key: ' . (string) $this->config['brevo_api_key'],
        ];

        if (function_exists('curl_init')) {
            $curl = curl_init('https://api.brevo.com/v3/smtp/email');
            curl_setopt_array($curl, [
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 20,
            ]);
            $response = curl_exec($curl);
            $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);
            curl_close($curl);

            if ($status >= 200 && $status < 300) {
                return true;
            }

            $this->lastSmtpError = 'API Brevo refusée (' . $status . ' ' . $this->safeErrorMessage($error !== '' ? $error : (string) $response) . ')';
            return false;
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => $payload,
                'timeout' => 20,
                'ignore_errors' => true,
            ],
        ]);
        $response = @file_get_contents('https://api.brevo.com/v3/smtp/email', false, $context);
        $statusLine = $http_response_header[0] ?? '';
        if (preg_match('/\s(2\d\d)\s/', $statusLine)) {
            return true;
        }

        $this->lastSmtpError = 'API Brevo refusée (' . $this->safeErrorMessage($statusLine . ' ' . (string) $response) . ')';
        return false;
    }

    private function logDemo(string $to, string $subject, string $event, string $preview): void
    {
        $this->writeLog([
            'date' => date('c'),
            'mode' => 'demo',
            'event' => $event,
            'recipient' => $to,
            'subject' => $subject,
            'preview' => mb_substr(preg_replace('/\s+/', ' ', $preview) ?: '', 0, 300),
        ]);
        $this->logSafe('mail_demo_logged', $to, $subject, $event, 'Email journalisé en mode démonstration.');
    }

    private function logSafe(string $action, string $to, string $subject, string $event, string $message): void
    {
        $this->writeLog([
            'date' => date('c'),
            'mode' => $action,
            'event' => $event,
            'recipient' => $to,
            'subject' => $subject,
            'message' => $message,
        ]);
        if (function_exists('audit')) {
            audit(null, $action, 'mail', null);
        }
    }

    private function writeLog(array $data): void
    {
        $directory = dirname(__DIR__, 2) . '/storage/logs';
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        file_put_contents($directory . '/mail-demo.log', json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND | LOCK_EX);
        error_log('[AtypikHouse mail] ' . json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    private function safeErrorMessage(string $message): string
    {
        $password = (string) ($this->config['smtp']['password'] ?? '');
        $username = (string) ($this->config['smtp']['username'] ?? '');
        $message = str_replace([$password, $username], ['[secret]', '[smtp-user]'], $message);
        return mb_substr($message, 0, 300);
    }

    private function encodeHeader(string $value): string
    {
        return '=?UTF-8?B?' . base64_encode($value) . '?=';
    }

    private function headerAddress(string $name, string $email): string
    {
        return $this->encodeHeader($name) . ' <' . $email . '>';
    }
}
