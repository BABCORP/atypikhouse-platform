<?php

$firstEnv = static function (array $keys, ?string $default = null): ?string {
    foreach ($keys as $key) {
        $value = getenv($key);
        if ($value !== false && $value !== '') {
            return (string) $value;
        }
    }

    return $default;
};

$databaseUrl = $firstEnv(['DATABASE_URL', 'MYSQL_URL', 'MYSQL_PUBLIC_URL'], '');
$parsed = $databaseUrl !== '' ? parse_url($databaseUrl) : [];
$database = '';

if (is_array($parsed) && !empty($parsed['path'])) {
    $database = ltrim((string) $parsed['path'], '/');
}

return [
    'host' => $firstEnv(['DB_HOST', 'MYSQLHOST'], is_array($parsed) ? ($parsed['host'] ?? '127.0.0.1') : '127.0.0.1'),
    'port' => $firstEnv(['DB_PORT', 'MYSQLPORT'], is_array($parsed) ? (string) ($parsed['port'] ?? '3306') : '3306'),
    'database' => $firstEnv(['DB_DATABASE', 'MYSQLDATABASE'], $database ?: 'atypikhouse'),
    'username' => $firstEnv(['DB_USERNAME', 'MYSQLUSER'], is_array($parsed) && isset($parsed['user']) ? urldecode((string) $parsed['user']) : 'root'),
    'password' => $firstEnv(['DB_PASSWORD', 'MYSQLPASSWORD'], is_array($parsed) && isset($parsed['pass']) ? urldecode((string) $parsed['pass']) : ''),
    'charset' => 'utf8mb4',
    'socket' => $firstEnv(['DB_SOCKET'], ''),
    'ssl_ca' => $firstEnv(['DB_SSL_CA', 'MYSQL_ATTR_SSL_CA'], ''),
    'ssl_verify' => $firstEnv(['DB_SSL_VERIFY'], 'false') === 'true',
];
