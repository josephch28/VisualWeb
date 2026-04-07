<?php

namespace App\Infrastructure\Database;

use PDO;
use PDOException;

class PDOConnection
{
    private static ?PDO $instance = null;

    private function __construct() { }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $host = 'localhost';
            $db   = 'cuartouta';
            $user = 'root';
            $pass = '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                throw new PDOException("Connection to database failed: " . $e->getMessage(), (int)$e->getCode());
            }
        }

        return self::$instance;
    }
}
