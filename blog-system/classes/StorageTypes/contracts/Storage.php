<?php
namespace classes\StorageTypes\contracts;

interface Storage
{
    // CRUD Operations
    public function insert(string $table, array $data): bool;
    public function update(string $table, int $id, array $data): bool;
    public function delete(string $table, int $id): bool;
    public function getById(string $table, int $id): ?array;
    public function getAll(string $table): array;
    public function getAllPaginated(string $table, int $page, int $pageSize): array;
    public function getByField(string $table, string $field, $value): array;
    
    // Utility Methods
    public function getLastInsertId(): int;
    public function countAll(string $table): int;
    public function sumField(string $table, string $field): int;
    
    // Transaction Methods
    public function startTransaction(): bool;
    public function commit(): bool;
    public function rollback(): bool;
}