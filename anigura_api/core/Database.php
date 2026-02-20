<?php
namespace Core;

use PDO, PDOException;

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $host    = Config::get("DB_HOST");
        $port    = Config::get("DB_PORT");
        $db_name = Config::get("DB_NAME");
        $user    = Config::get("DB_USER");
        $pass    = Config::get("DB_PASS");
        $charset = Config::get("DB_CHARSET");

        $dsn = "mysql:host=$host;port=$port;dbname=$db_name;charset=$charset";

        try {
            $this->connection = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);

        } catch (PDOException $e) {
            Response::json(500, null, "Database Connect Error: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance->connection;
    }
}