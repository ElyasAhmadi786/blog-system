<?php
namespace classes\StorageTypes;

use classes\StorageTypes\contracts\Storage;
use classes\Database;

class MySQL implements Storage
{
    private $connection;
    
    public function __construct()
    {
        $this->connection = Database::getConnection();
    }

    public function insert(string $table, array $data): bool
    {
        $cols = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $query = "INSERT INTO `$table` ($cols) VALUES ($placeholders)";
        
        $stmt = $this->connection->prepare($query);
        if (!$stmt) return false;

        $types = $this->getParamTypes($data);
        $values = array_values($data);
        $stmt->bind_param($types, ...$values);

        return $stmt->execute();
    }

    public function update(string $table, int $id, array $data): bool
    {
        $setClause = implode(', ', array_map(function($key) {
            return "`$key` = ?";
        }, array_keys($data)));
        
        $query = "UPDATE `$table` SET $setClause WHERE `id` = ?";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) return false;

        $types = $this->getParamTypes($data) . 'i';
        $values = array_values($data);
        $values[] = $id;
        
        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }

    public function delete(string $table, int $id): bool
    {
        $query = "DELETE FROM `$table` WHERE `id` = ?";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) return false;

        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getById(string $table, int $id): ?array
    {
        $query = "SELECT * FROM `$table` WHERE `id` = ?";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) return null;

        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($result && $result->num_rows === 1) {
            return $result->fetch_assoc();
        }
        
        return null;
    }

    public function getAll(string $table): array
    {
        $query = "SELECT * FROM `$table`";
        $result = $this->connection->query($query);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        return [];
    }

    public function getAllPaginated(string $table, int $page, int $pageSize): array
    {
        $offset = max(0, ($page - 1) * $pageSize);
        $query = "SELECT * FROM `$table` ORDER BY `created_at` DESC LIMIT ?, ?";
        
        $stmt = $this->connection->prepare($query);
        if (!$stmt) return [];

        $stmt->bind_param("ii", $offset, $pageSize);
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        return [];
    }

    public function getByField(string $table, string $field, $value): array
    {
        $query = "SELECT * FROM `$table` WHERE `$field` = ? ORDER BY `created_at` DESC";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) return [];

        $type = $this->getParamTypes([$value]);
        $stmt->bind_param($type, $value);
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        return [];
    }

    public function getLastInsertId(): int
    {
        return $this->connection->insert_id;
    }

    public function countAll(string $table): int
    {
        $result = $this->connection->query("SELECT COUNT(*) as cnt FROM `$table`");
        return ($result) ? (int)$result->fetch_assoc()['cnt'] : 0;
    }

    public function sumField(string $table, string $field): int
    {
        // Sanitise field name to only allow safe characters
        $safeField = preg_replace('/[^a-zA-Z0-9_]/', '', $field);
        $result    = $this->connection->query(
            "SELECT COALESCE(SUM(`$safeField`), 0) AS total FROM `$table`"
        );
        return ($result) ? (int)$result->fetch_assoc()['total'] : 0;
    }

    private function getParamTypes(array $data): string
    {
        $types = '';
        foreach ($data as $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        return $types;
    }

    public function startTransaction(): bool
    {
        return $this->connection->begin_transaction();
    }
    
    public function commit(): bool
    {
        return $this->connection->commit();
    }
    
    public function rollback(): bool
    {
        return $this->connection->rollback();
    }
}