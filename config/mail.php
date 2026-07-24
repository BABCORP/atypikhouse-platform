<?php

$bool = static fn (mixed $value, bool $default = false): bool => filter_var(
    $value === null ? $default : $value,
    FILTER_VALIDATE_BOOLEAN
);

$smtpEnabled = $bool(env_value('SMTP_ENABLED', false));
$realEmailSending = $bool(env_value('REAL_EMAIL_SENDING', false));
$realSendingRequested = $smtpEnabled || $realEmailSending;

$host = env_value('MAIL_HOST', env_value('SMTP_HOST', 'smtp-relay.brevo.com'));
$username = env_value('MAIL_USERNAME', env_value('SMTP_USERNAME', ''));
$fromEmail = env_value('MAIL_FROM_ADDRESS', env_value('SMTP_FROM_EMAIL', $username ?: 'contact@atypikhouse.fr'));

return [
    'mailer' => env_value('MAIL_MAILER', 'smtp'),
    'enabled' => $smtpEnabled,
    'real_email_sending' => $realEmailSending,
    'demo_mode' => $bool(env_value('MAIL_DEMO_MODE', null), !$realSendingRequested),
    'log_only' => $bool(env_value('MAIL_LOG_ONLY', null), !$realSendingRequested),
    'app_url' => rtrim((string) env_value('APP_URL', 'http://localhost:8000'), '/'),
    'admin_email' => env_value('ADMIN_EMAIL', env_value('ADMIN_NOTIFICATION_EMAIL', $fromEmail)),
    'support_email' => env_value('SUPPORT_EMAIL', 'support@atypikhouse.fr'),
    'dpo_email' => env_value('DPO_EMAIL', 'dpo@atypikhouse.fr'),
    'smtp' => [
        'host' => $host,
        'port' => (int) env_value('MAIL_PORT', env_value('SMTP_PORT', 587)),
        'encryption' => env_value('MAIL_ENCRYPTION', env_value('SMTP_ENCRYPTION', 'tls')),
        'username' => $username,
        'password' => env_value('MAIL_PASSWORD', env_value('SMTP_PASSWORD', '')),
    ],
    'from' => [
        'email' => $fromEmail,
        'name' => env_value('MAIL_FROM_NAME', env_value('SMTP_FROM_NAME', 'AtypikHouse')),
    ],
];
