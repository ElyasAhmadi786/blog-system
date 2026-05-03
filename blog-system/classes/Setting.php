<?php

namespace classes;

use classes\Repository\SettingRepository;

class Setting
{
    private ?int $id;
    private string $title;
    private ?string $keywords;
    private ?string $description;
    private ?string $author;
    private ?string $logo;
    private ?string $footer;
    private SettingRepository $settingRepository;

    public function __construct(
        ?int $id = null,
        string $title,
        ?string $keywords = null,
        ?string $description = null,
        ?string $author = null,
        ?string $logo = null,
        ?string $footer = null,
        SettingRepository $settingRepository
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->keywords = $keywords;
        $this->description = $description;
        $this->author = $author;
        $this->logo = $logo;
        $this->footer = $footer;
        $this->settingRepository = $settingRepository;
    }

    public function save(): bool
    {
        $data = [
            'title' => $this->title,
            'keywords' => $this->keywords,
            'description' => $this->description,
            'author' => $this->author,
            'logo' => $this->logo,
            'footer' => $this->footer
        ];

        if ($this->id) {
            return $this->settingRepository->update($this->id, $data);
        } else {
            return $this->settingRepository->store($data);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function getFooter(): ?string
    {
        return $this->footer;
    }
}
