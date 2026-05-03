<?php
namespace classes\Repository;

use classes\StorageTypes\contracts\Storage;

class BaseRepository
{
    protected string $tableName;
    protected Storage $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function getAll(): array
    {
        return $this->storage->getAll($this->tableName);
    }

    public function store(array $data): bool
    {
        return $this->storage->insert($this->tableName, $data);
    }

    public function getAllPaginated(int $page, int $pageSize): array
    {
        return $this->storage->getAllPaginated($this->tableName, $page, $pageSize);
    }

    public function getById(int $id): ?array
    {
        return $this->storage->getById($this->tableName, $id);
    }

    public function update(int $id, array $data): bool
    {
        return $this->storage->update($this->tableName, $id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->storage->delete($this->tableName, $id);
    }

    public function getByField(string $field, $value): array
    {
        return $this->storage->getByField($this->tableName, $field, $value);
    }

    public function getLastInsertId(): int
    {
        return $this->storage->getLastInsertId();
    }
}