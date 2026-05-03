<?php
namespace classes\Repository;

class SettingRepository extends BaseRepository
{
    protected string $tableName = "settings";
    
    public function __construct($storage)
    {
        parent::__construct($storage);
    }

    public function getSettings(): ?array
    {
        $settings = $this->getAll();
        return !empty($settings) ? $settings[0] : null;
    }
}