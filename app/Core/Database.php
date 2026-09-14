<?php
namespace App\Core;

class Database
{
    private static ?\PDO $instance = null;
    private string $host;
    private string $user;
    private string $pass;
    private string $dbname;
    private string $charset;

    public function __construct(string $host = 'localhost', string $user = 'root', string $pass = '', string $dbname = 'smart_clothing', string $charset = 'utf8mb4')
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->dbname = $dbname;
        $this->charset = $charset;
    }

    public static function getInstance(?string $host = null, ?string $user = null, ?string $pass = null, ?string $dbname = null, ?string $charset = null): \PDO
    {
        if (self::$instance === null) {
            self::$instance = (new self($host ?? 'localhost', $user ?? 'root', $pass ?? '', $dbname ?? 'smart_clothing', $charset ?? 'utf8mb4'))->connect();
        }
        return self::$instance;
    }

    public function connect(): \PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];
        try {
            self::$instance = new \PDO($dsn, $this->user, $this->pass, $options);
        } catch (\PDOException $e) {
            die("Database Connection Failed: " . htmlspecialchars($e->getMessage()));
        }
        return self::$instance;
    }

    public function getPdo(): \PDO
    {
        return self::getInstance();
    }
}
