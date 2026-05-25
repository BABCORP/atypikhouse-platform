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
            self::$pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
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
