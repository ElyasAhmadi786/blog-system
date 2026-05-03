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

    /**
     * Return a page of posts optionally filtered by status and/or title search.
     * Filtering is done in PHP because the dataset is small; avoids changing the
     * Storage contract with a complex query method.
     */
    public function getFiltered(?string $status, ?string $search, int $page, int $perPage): array
    {
        $all = $this->getAll();
        $all = $this->applyFilters($all, $status, $search);
        return array_slice(array_values($all), ($page - 1) * $perPage, $perPage);
    }

    public function countFiltered(?string $status, ?string $search): int
    {
        $all = $this->getAll();
        return count($this->applyFilters($all, $status, $search));
    }

    private function applyFilters(array $posts, ?string $status, ?string $search): array
    {
        if ($status) {
            $posts = array_filter($posts, function ($p) use ($status) {
                return ($p['status'] ?? 'published') === $status;
            });
        }
        if ($search) {
            $posts = array_filter($posts, function ($p) use ($search) {
                return stripos($p['title'], $search) !== false;
            });
        }
        return $posts;
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