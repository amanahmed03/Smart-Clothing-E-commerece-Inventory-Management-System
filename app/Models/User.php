<?php
namespace App\Models;

use App\Core\Database;

class User
{
    protected \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, name, email, role, phone, address, created_at FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT id, name, email, role, phone, created_at FROM users ORDER BY id ASC');
        return $stmt->fetchAll();
    }

    public function create(string $name, string $email, string $password, string $role = 'customer'): bool
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)');
        return $stmt->execute([':name' => $name, ':email' => $email, ':password' => $hashedPassword, ':role' => $role]);
    }

    public function update(int $id, string $name, string $role): bool
    {
        $stmt = $this->pdo->prepare('UPDATE users SET name = :name, role = :role, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        return $stmt->execute([':name' => $name, ':role' => $role, ':id' => $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function getUserCounts(): array
    {
        $stmt = $this->pdo->query("
            SELECT COUNT(*) AS total_users,
                   SUM(CASE WHEN role = 'customer' THEN 1 ELSE 0 END) AS total_customers,
                   SUM(CASE WHEN role = 'inventory_manager' THEN 1 ELSE 0 END) AS total_inventory_managers,
                   SUM(CASE WHEN role = 'delivery_manager' THEN 1 ELSE 0 END) AS total_delivery_managers,
                   SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) AS total_admins
            FROM users
        ");
        return $stmt->fetch();
    }

    public function getRecentUsers(int $limit = 5): array
    {
        $stmt = $this->pdo->prepare('SELECT id, name, email, role, created_at FROM users ORDER BY id DESC LIMIT :limit');
        $stmt->execute([':limit' => $limit]);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, name, email, role, phone, address FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }
}
