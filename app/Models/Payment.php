<?php
namespace App\Models;

use App\Core\Database;

class Payment
{
    protected \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }
}
