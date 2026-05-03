<?php
namespace classes\Repository;

class PostRepository extends BaseRepository
{
    protected string $tableName = "posts";
    
    public function __construct($storage)
    {
        parent::__construct($storage);
    }

    public function getByCategory(string $category): array
    {
        $posts = $this->getByField('category', $category);
        return $this->processImagePaths($posts);
    }

    public function getTopPosts(int $limit = 5): array
    {
        $allPosts = $this->getAll();
        
        usort($allPosts, function($a, $b) {
            return $b['views'] - $a['views'];
        });
        
        $topPosts = array_slice($allPosts, 0, $limit);
        return $this->processImagePaths($topPosts);
    }

    public function getAll(): array
    {
        $posts = parent::getAll();
        return $this->processImagePaths($posts);
    }

    public function getAllPaginated(int $page, int $pageSize): array
    {
        $posts = parent::getAllPaginated($page, $pageSize);
        return $this->processImagePaths($posts);
    }

    public function getById(int $id): ?array
    {
        $post = parent::getById($id);
        
        if ($post) {
            $post = $this->processImagePath($post);
        }
        
        return $post;
    }

    private function processImagePaths(array $posts): array
    {
        return array_map(function($post) {
            return $this->processImagePath($post);
        }, $posts);
    }

    private function processImagePath(array $post): array
    {
        if (!empty($post['image'])) {
            $post['image'] = basename($post['image']);
        }
        return $post;
    }
}