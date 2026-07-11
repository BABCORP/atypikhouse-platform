<?php

return [
    'mailer' => env_value('MAIL_MAILER', 'smtp'),
    'demo_mode' => filter_var(env_value('MAIL_DEMO_MODE', true), FILTER_VALIDATE_BOOLEAN),
    'log_only' => filter_var(env_value('MAIL_LOG_ONLY', true), FILTER_VALIDATE_BOOLEAN),
    'admin_email' => env_value('ADMIN_NOTIFICATION_EMAIL', env_value('SMTP_FROM_EMAIL', 'contact@atypikhouse.fr')),
    'smtp' => [
        'host' => env_value('SMTP_HOST', 'smtp.gmail.com'),
        'port' => (int) env_value('SMTP_PORT', 587),
        'encryption' => env_value('SMTP_ENCRYPTION', 'tls'),
        'username' => env_value('SMTP_USERNAME', ''),
        'password' => env_value('SMTP_PASSWORD', ''),
    ],
    'from' => [
        'email' => env_value('SMTP_FROM_EMAIL', 'contact@atypikhouse.fr'),
        'name' => env_value('SMTP_FROM_NAME', 'AtypikHouse'),
    ],
];
