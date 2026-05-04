<?php
namespace classes\Repository;

class UserRepository extends BaseRepository
{
    protected string $tableName = "users";

    public function __construct($storage)
    {
        parent::__construct($storage);
    }

    /** Return the total number of registered users. */
    public function countUsers(): int
    {
        return $this->countAll();
    }
}
