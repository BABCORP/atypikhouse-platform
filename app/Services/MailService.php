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
            if (!$sent) {
                $sent = $this->sendViaGmailFallbacks($to, $subject, $htmlBody, $textBody);
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
            'Nouveau message de contact',
            'Un nouveau message de contact a été enregistré.',
            'contact_form_submit',
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
            'Votre compte AtypikHouse est en attente de validation',
            '<p>Bonjour ' . e($firstName) . ',</p>'
            . '<p>Votre compte AtypikHouse a bien été créé.</p>'
            . '<p>Pour garantir la sécurité de la plateforme, il doit maintenant être validé par un administrateur.</p>'
            . '<p>Vous recevrez un nouvel email lorsque votre compte sera approuvé.</p>'
            . '<p>À bientôt,<br>L’équipe AtypikHouse</p>'
            . '<p><strong>Projet étudiant fictif.</strong> Aucun achat, paiement ou réservation réelle ne peut être effectué.</p>',
            null,
            'account_pending_user'
        );
        $this->logInternalNotification(
            'Nouveau compte à valider sur AtypikHouse',
            'Un nouveau compte vient d’être créé et attend une validation administrateur.',
            'account_pending_admin',
            [
                'Prénom' => $firstName,
                'Email' => $to,
                'Rôle demandé' => $roleLabel,
                'Lien back-office' => $this->config['app_url'] . '/admin/utilisateurs',
            ]
        );

        return $userSent;
    }

    public function sendAccountApprovedNotification(string $to, string $firstName): bool
    {
        return $this->send(
            $to,
            'Votre compte AtypikHouse a été validé',
            '<p>Bonjour ' . e($firstName) . ',</p>'
            . '<p>Votre compte AtypikHouse a été validé par l’administrateur.</p>'
            . '<p>Vous pouvez maintenant vous connecter et accéder à votre espace.</p>'
            . '<p>Lien de connexion : <a href="' . e($this->config['app_url'] . '/connexion') . '">' . e($this->config['app_url'] . '/connexion') . '</a></p>'
            . '<p>À bientôt,<br>L’équipe AtypikHouse</p>'
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
            'smtp_host' => (string) $this->config['smtp']['host'],
            'smtp_port' => (int) $this->config['smtp']['port'],
            'smtp_encryption' => (string) $this->config['smtp']['encryption'],
            'smtp_username' => (string) $this->config['smtp']['username'],
        ];
    }

    public function sendPropertySubmittedNotification(string $ownerEmail, string $propertyTitle): bool
    {
        $ownerSent = $this->send(
            $ownerEmail,
            'Votre logement a été soumis à validation',
            '<p>Votre logement "' . e($propertyTitle) . '" a bien été soumis à validation par l’équipe AtypikHouse.</p>',
            null,
            'property_submitted_owner'
        );
        $this->logInternalNotification(
            'Nouveau logement à valider',
            'Un propriétaire a soumis un logement à validation.',
            'property_submitted_admin',
            ['Logement' => $propertyTitle, 'Propriétaire' => $ownerEmail]
        );
        return $ownerSent;
    }

    public function sendPropertyApprovedNotification(string $ownerEmail, string $propertyTitle): bool
    {
        return $this->send(
            $ownerEmail,
            'Votre logement a été validé et publié',
            '<p>Votre logement "' . e($propertyTitle) . '" a été validé et publié dans le catalogue AtypikHouse.</p>',
            null,
            'property_approved'
        );
    }

    public function sendBookingPendingNotification(string $tenantEmail, string $propertyTitle): bool
    {
        $tenantSent = $this->send(
            $tenantEmail,
            'Votre réservation fictive est en attente de validation',
            '<p>Votre réservation fictive pour "' . e($propertyTitle) . '" a été créée et attend la validation de l’administrateur.</p>',
            null,
            'booking_pending_tenant'
        );
        $this->logInternalNotification(
            'Nouvelle réservation en attente de validation',
            'Une réservation fictive attend une décision administrateur.',
            'booking_pending_admin',
            ['Logement' => $propertyTitle, 'Locataire' => $tenantEmail]
        );
        return $tenantSent;
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

    private function sendViaGmailFallbacks(string $to, string $subject, string $htmlBody, string $textBody): bool
    {
        if ((string) $this->config['smtp']['host'] !== 'smtp.gmail.com') {
            return false;
        }

        $originalPort = (int) $this->config['smtp']['port'];
        $originalEncryption = strtolower((string) $this->config['smtp']['encryption']);
        $fallbacks = [
            ['port' => 587, 'encryption' => 'tls'],
            ['port' => 465, 'encryption' => 'ssl'],
        ];

        foreach ($fallbacks as $fallback) {
            if ($fallback['port'] === $originalPort && $fallback['encryption'] === $originalEncryption) {
                continue;
            }

            $this->config['smtp']['port'] = $fallback['port'];
            $this->config['smtp']['encryption'] = $fallback['encryption'];
            $this->lastSmtpError .= ' Nouvelle tentative avec smtp.gmail.com:' . $fallback['port'] . ' ' . $fallback['encryption'] . '.';
            if ($this->sendViaSmtp($to, $subject, $htmlBody, $textBody)) {
                $this->config['smtp']['port'] = $originalPort;
                $this->config['smtp']['encryption'] = $originalEncryption;
                return true;
            }
        }

        $this->config['smtp']['port'] = $originalPort;
        $this->config['smtp']['encryption'] = $originalEncryption;
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
