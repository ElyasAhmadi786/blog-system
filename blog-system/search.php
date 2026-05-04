<?php
require_once "./autoload.php";

use classes\Repository\PostRepository;
use classes\Repository\SettingRepository;
use classes\StorageTypes\MySQL;

$postRepository    = new PostRepository(new MySQL());
$settingRepository = new SettingRepository(new MySQL());

$query    = trim($_GET['search'] ?? '');
$page     = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage  = 10;
$settings = $settingRepository->getSettings();
$topPosts = $postRepository->getTopPosts(7);

// Search across all posts in PHP (dataset is small)
$results = [];
if ($query !== '') {
    $allPosts = $postRepository->getAll();
    foreach ($allPosts as $post) {
        if (stripos($post['title'], $query) !== false
            || stripos(strip_tags($post['content']), $query) !== false) {
            $results[] = $post;
        }
    }
}

// Paginate results
$total       = count($results);
$totalPages  = $perPage > 0 ? (int)ceil($total / $perPage) : 1;
$page        = max(1, min($page, max(1, $totalPages)));
$paginated   = array_slice($results, ($page - 1) * $perPage, $perPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - <?= htmlspecialchars($settings['title'] ?? 'Gitmag website') ?></title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        .search-header {
            margin-bottom: 20px;
        }
        .search-count {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        .pagination {
            text-align: center;
            margin: 20px 0;
        }
        .pagination a {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 5px;
            background: #f38b8b;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .pagination a:hover {
            background: #e07b7b;
        }
        .admin-login {
            float: right;
            margin-right: 20px;
        }
        .admin-login a {
            color: #f38b8b;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <main>
        <header>
            <h1><?= htmlspecialchars($settings['title'] ?? 'Gitmag website') ?></h1>
            <?php if (!empty($settings['logo'])): ?>
            <div id="logo">
                <img src="./assets/images/<?= htmlspecialchars(basename($settings['logo'])) ?>"
                     alt="<?= htmlspecialchars($settings['title'] ?? 'Logo') ?>">
            </div>
            <?php endif; ?>
        </header>
        <nav>
            <ul>
                <li><a href="./index.php">Home</a></li>
                <li><a href="./about.php">About us</a></li>
                <li><a href="./blog.php">Blog</a></li>
                <li><a href="./gallery.php">Gallery</a></li>
                <li><a href="./contact.php">Contact us</a></li>
            </ul>
            <div class="admin-login">
                <a href="./login.php">Admin Panel</a>
            </div>
            <form action="search.php" method="GET">
                <input type="text" name="search" placeholder="Search your word"
                       value="<?= htmlspecialchars($query) ?>">
                <input type="submit" value="Search">
            </form>
        </nav>
        <section id="content">
            <aside>
                <div class="aside-box">
                    <h2>Top Posts</h2>
                    <ul>
                        <?php foreach ($topPosts as $post): ?>
                        <li><a href="post.php?id=<?= (int)$post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </aside>
            <div id="articles">
                <div class="search-header">
                    <h2>Search Results</h2>
                    <?php if ($query !== ''): ?>
                        <p class="search-count">
                            <?= $total ?> result<?= $total !== 1 ? 's' : '' ?> for
                            "<strong><?= htmlspecialchars($query) ?></strong>"
                        </p>
                    <?php else: ?>
                        <p class="search-count">Please enter a search term.</p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($paginated)): ?>
                    <?php foreach ($paginated as $post): ?>
                    <article>
                        <div class="caption">
                            <h3><?= htmlspecialchars($post['title']) ?></h3>
                            <ul class="post-meta">
                                <li>📅 <?= date('Y-m-d', strtotime($post['created_at'])) ?></li>
                                <li>👁️ <?= (int)$post['views'] ?> views</li>
                                <li>🏷️ <?= htmlspecialchars(ucfirst($post['category'])) ?></li>
                            </ul>
                            <p><?= htmlspecialchars(substr(strip_tags($post['content']), 0, 200)) ?>...</p>
                            <a href="post.php?id=<?= (int)$post['id'] ?>" class="read-more">Read More →</a>
                        </div>
                        <?php if (!empty($post['image'])): ?>
                        <div class="image">
                            <img src="./assets/images/<?= htmlspecialchars(basename($post['image'])) ?>"
                                 alt="<?= htmlspecialchars($post['title']) ?>"
                                 onerror="this.src='./assets/images/default.jpg'">
                        </div>
                        <?php endif; ?>
                        <div class="clearfix"></div>
                    </article>
                    <?php endforeach; ?>

                    <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?search=<?= urlencode($query) ?>&page=<?= $page - 1 ?>">← Previous</a>
                        <?php endif; ?>
                        <span>Page <?= $page ?> of <?= $totalPages ?></span>
                        <?php if ($page < $totalPages): ?>
                            <a href="?search=<?= urlencode($query) ?>&page=<?= $page + 1 ?>">Next →</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                <?php elseif ($query !== ''): ?>
                    <article>
                        <div class="caption">
                            <h3>No Results Found</h3>
                            <p>No posts match your search for "<?= htmlspecialchars($query) ?>". Try different keywords.</p>
                        </div>
                    </article>
                <?php endif; ?>
            </div>
            <div class="clearfix"></div>
        </section>
        <footer>
            <p><?= htmlspecialchars($settings['footer'] ?? 'Copyright 2024 Gitmag Website. All rights reserved.') ?></p>
        </footer>
    </main>
</body>
</html>
