<?php

namespace App\Services;

use Throwable;

final class MailService
{
    private array $config;

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
            $sent = $this->sendViaSmtp($to, $subject, $htmlBody, $textBody);
            $this->logSafe($sent ? 'mail_send_success' : 'mail_send_failed', $to, $subject, $event, $sent ? 'Email envoyé.' : 'Échec SMTP.');
            return $sent;
        } catch (Throwable $exception) {
            $this->logSafe('mail_send_failed', $to, $subject, $event, 'Erreur SMTP sans détail sensible.');
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
            '<p>Bonjour ' . e($firstName) . ',</p><p>Votre compte ' . e($roleLabel) . ' a bien été créé. Il sera vérifié par l’équipe AtypikHouse avant utilisation complète.</p>',
            null,
            'account_pending_user'
        );
        $adminSent = $this->sendInternalNotification(
            'Nouveau compte à valider',
            'Un nouveau compte utilisateur attend une validation administrateur.',
            'account_pending_admin',
            ['Email' => $to, 'Rôle' => $roleLabel]
        );
        return $userSent && $adminSent;
    }

    public function sendAccountApprovedNotification(string $to, string $firstName): bool
    {
        return $this->send(
            $to,
            'Votre compte AtypikHouse a été validé',
            '<p>Bonjour ' . e($firstName) . ',</p><p>Votre compte AtypikHouse est maintenant validé. Vous pouvez vous connecter à votre espace.</p>',
            null,
            'account_approved'
        );
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
        $adminSent = $this->sendInternalNotification(
            'Nouveau logement à valider',
            'Un propriétaire a soumis un logement à validation.',
            'property_submitted_admin',
            ['Logement' => $propertyTitle, 'Propriétaire' => $ownerEmail]
        );
        return $ownerSent && $adminSent;
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
        $adminSent = $this->sendInternalNotification(
            'Nouvelle réservation en attente de validation',
            'Une réservation fictive attend une décision administrateur.',
            'booking_pending_admin',
            ['Logement' => $propertyTitle, 'Locataire' => $tenantEmail]
        );
        return $tenantSent && $adminSent;
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

    private function shouldLogOnly(): bool
    {
        return (bool) $this->config['demo_mode'] || (bool) $this->config['log_only'];
    }

    private function smtpReady(): bool
    {
        return trim((string) $this->config['smtp']['host']) !== ''
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
            return false;
        }

        $read = static fn (): string => (string) fgets($socket, 515);
        $write = static function (string $command) use ($socket): void {
            fwrite($socket, $command . "\r\n");
        };
        $expect = static function (array $codes) use ($read): bool {
            $response = $read();
            return in_array(substr($response, 0, 3), $codes, true);
        };

        if (!$expect(['220'])) {
            fclose($socket);
            return false;
        }
        $write('EHLO atypikhouse.local');
        while (($line = $read()) !== '' && isset($line[3]) && $line[3] === '-') {
            // consume multiline response
        }
        if ($encryption === 'tls') {
            $write('STARTTLS');
            if (!$expect(['220']) || !stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                fclose($socket);
                return false;
            }
            $write('EHLO atypikhouse.local');
            while (($line = $read()) !== '' && isset($line[3]) && $line[3] === '-') {
                // consume multiline response
            }
        }
        $write('AUTH LOGIN');
        if (!$expect(['334'])) {
            fclose($socket);
            return false;
        }
        $write(base64_encode((string) $this->config['smtp']['username']));
        if (!$expect(['334'])) {
            fclose($socket);
            return false;
        }
        $write(base64_encode((string) $this->config['smtp']['password']));
        if (!$expect(['235'])) {
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
        $ok = $expect(['250']);
        $write('RCPT TO:<' . $to . '>');
        $ok = $ok && $expect(['250', '251']);
        $write('DATA');
        $ok = $ok && $expect(['354']);
        $write($message . "\r\n.");
        $ok = $ok && $expect(['250']);
        $write('QUIT');
        fclose($socket);
        return $ok;
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
