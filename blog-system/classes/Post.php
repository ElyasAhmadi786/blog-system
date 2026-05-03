<?php
namespace classes;

use classes\Repository\PostRepository;

class Post
{
    private ?int $id;
    private string $title;
    private string $content;
    private string $category;
    private ?string $image;
    private int $views;
    private PostRepository $postRepository;

    public function __construct(
        ?int $id = null,
        string $title,
        string $content,
        string $category,
        ?string $image = null,
        PostRepository $postRepository
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->category = $category;
        $this->image = $image;
        $this->views = 0;
        $this->postRepository = $postRepository;
    }

    public function save(): bool
    {
        $data = [
            'title' => $this->title,
            'content' => $this->content,
            'category' => $this->category,
            'image' => $this->image,
            'views' => $this->views
        ];

        if ($this->id) {
            return $this->postRepository->update($this->id, $data);
        } else {
            return $this->postRepository->store($data);
        }
    }

    public function delete(): bool
    {
        if ($this->id) {
            return $this->postRepository->delete($this->id);
        }
        return false;
    }

    public function incrementViews(): void
    {
        $this->views++;
        if ($this->id) {
            $this->postRepository->update($this->id, ['views' => $this->views]);
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

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }
}