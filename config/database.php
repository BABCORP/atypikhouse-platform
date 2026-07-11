<?php

$databaseUrl = getenv('DATABASE_URL') ?: getenv('MYSQL_URL') ?: '';
$parsed = $databaseUrl !== '' ? parse_url($databaseUrl) : [];
$database = '';

if (!empty($parsed['path'])) {
    $database = ltrim((string) $parsed['path'], '/');
}

return [
    'host' => getenv('DB_HOST') ?: ($parsed['host'] ?? '127.0.0.1'),
    'port' => getenv('DB_PORT') ?: (string) ($parsed['port'] ?? '3306'),
    'database' => getenv('DB_DATABASE') ?: ($database ?: 'atypikhouse'),
    'username' => getenv('DB_USERNAME') ?: ($parsed['user'] ?? 'root'),
    'password' => getenv('DB_PASSWORD') ?: (isset($parsed['pass']) ? urldecode((string) $parsed['pass']) : ''),
    'charset' => 'utf8mb4',
    'socket' => getenv('DB_SOCKET') ?: '',
    'ssl_ca' => getenv('DB_SSL_CA') ?: '',
    'ssl_verify' => (getenv('DB_SSL_VERIFY') ?: 'false') === 'true',
];
