<?php

namespace Src\Models;

class Database
{
    public function __construct(
        private $dbConnection = null
    ) {
        $host = $_ENV['DB_HOST'] ?? null;
        $port = $_ENV['DB_PORT'] ?? null;
        $db   = $_ENV['DB_NAME'] ?? null;
        $user = $_ENV['DB_USERNAME'] ?? null;
        $pass = $_ENV['DB_PASSWORD'] ?? null;

        try {
            $this->dbConnection = new \PDO(
                "mysql:host=$host;port=$port;charset=utf8mb4;dbname=$db",
                $user,
                $pass
            );
        }
        catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->dbConnection;
    }
}