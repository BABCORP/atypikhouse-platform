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
            if ((require dirname(__DIR__, 2) . '/config/app.php')['debug']) {
                exit('Erreur de connexion base de données : ' . e($exception->getMessage()));
            }
            exit('Erreur serveur. Veuillez réessayer plus tard.');
        }

        return self::$pdo;
    }
}
