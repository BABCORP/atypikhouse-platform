<?php

namespace App\Core;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $config = require dirname(__DIR__, 2) . '/config/database.php';
        $dsn = $config['socket']
            ? sprintf('mysql:unix_socket=%s;dbname=%s;charset=%s', $config['socket'], $config['database'], $config['charset'])
            : sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $config['host'], $config['port'], $config['database'], $config['charset']);

        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            if (!empty($config['ssl_ca']) && defined('PDO::MYSQL_ATTR_SSL_CA')) {
                $options[PDO::MYSQL_ATTR_SSL_CA] = $config['ssl_ca'];
            }

            if (defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT')) {
                $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = (bool) ($config['ssl_verify'] ?? false);
            }

            self::$pdo = new PDO($dsn, $config['username'], $config['password'], $options);
        } catch (PDOException $exception) {
            http_response_code(500);
            $appConfig = require dirname(__DIR__, 2) . '/config/app.php';
            $logDir = dirname(__DIR__, 2) . '/storage/logs';
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0775, true);
            }
            @file_put_contents(
                $logDir . '/app.log',
                sprintf("[%s] Database connection failed: %s\n", date('c'), $exception->getMessage()),
                FILE_APPEND
            );

            if ($appConfig['debug']) {
                exit('Erreur de connexion base de données : ' . htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8'));
            }

            exit(self::renderProductionDatabaseError());
        }

        return self::$pdo;
    }

    private static function renderProductionDatabaseError(): string
    {
        return '<!doctype html><html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
            . '<title>AtypikHouse - Configuration en cours</title>'
            . '<style>body{margin:0;font-family:Arial,sans-serif;background:#fbf7f0;color:#173f2a}.wrap{max-width:760px;margin:12vh auto;padding:32px}.card{background:#fff;border:1px solid #eadfce;border-radius:14px;padding:28px;box-shadow:0 18px 50px rgba(23,63,42,.08)}h1{margin:0 0 12px;font-size:32px}p{line-height:1.6;color:#52645b}.hint{margin-top:18px;padding:14px 16px;background:#f2e6d8;border-radius:10px;font-weight:700}</style>'
            . '</head><body><main class="wrap"><section class="card">'
            . '<h1>Configuration de la base de données requise</h1>'
            . '<p>Le service AtypikHouse est bien démarré, mais la connexion MySQL n’est pas encore disponible. Sur Render, vérifiez la variable <strong>DATABASE_URL</strong> ou les variables <strong>DB_HOST</strong>, <strong>DB_DATABASE</strong>, <strong>DB_USERNAME</strong> et <strong>DB_PASSWORD</strong>.</p>'
            . '<p>Après configuration, importez <strong>database/schema.sql</strong> puis <strong>database/seed.sql</strong> dans la base MySQL et relancez le déploiement.</p>'
            . '<div class="hint">Diagnostic rapide : ouvrez <code>/healthz</code>. Si la page affiche <code>ok</code>, Docker et Apache fonctionnent.</div>'
            . '</section></main></body></html>';
    }
}
