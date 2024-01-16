<?php

namespace App\Core;

use PDO;
use PDOException;

class DB
{
    public static PDO $pdo;

    public static function new()
    {
        try {
            self::$pdo = new PDO('sqlite:db.sqlite');
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return self::$pdo;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
