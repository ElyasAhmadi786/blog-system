<?php
require_once "./autoload.php";

use classes\Repository\PostRepository;
use classes\Repository\SettingRepository;
use classes\StorageTypes\MySQL;

$postRepository = new PostRepository(new MySQL());
$settingRepository = new SettingRepository(new MySQL());

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$category = $_GET['category'] ?? '';
$perPage = 10;

if ($category) {
    $posts = $postRepository->getByCategory($category);
} else {
    $posts = $postRepository->getAllPaginated($page, $perPage);
}

$settings = $settingRepository->getSettings();
$topPosts = $postRepository->getTopPosts(7);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - <?= $settings['title'] ?? 'Gitmag website' ?></title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        .category-filter {
            margin: 20px 0;
            text-align: center;
        }
        .category-filter a {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 5px;
            background: #f0f0f0;
            color: #333;
            text-decoration: none;
            border-radius: 20px;
        }
        .category-filter a.active {
            background: #f38b8b;
            color: white;
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
    </style>
</head>
<body>
    <main>
        <header>
            <h1><?= $settings['title'] ?? 'Gitmag website' ?></h1>
            <?php if ($settings['logo']): ?>
            <div id="logo">
                <img src="./assets/images/logo.png" alt="Gitmag">
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
                <a href="./edit.php">Admin Panel</a>
            </div>
            <form action="search.php" method="GET">
                <input type="text" name="search" placeholder="Search your word">
                <input type="submit" value="Search">
            </form>
        </nav>
        <section id="content">
            <aside>
                <div class="aside-box">
                    <h2>Top Posts</h2>
                    <ul>
                        <?php foreach ($topPosts as $post): ?>
                        <li><a href="post.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="aside-box">
                    <h2>Categories</h2>
                    <ul>
                        <li><a href="blog.php?category=political" class="<?= $category === 'political' ? 'active' : '' ?>">Political</a></li>
                        <li><a href="blog.php?category=sport" class="<?= $category === 'sport' ? 'active' : '' ?>">Sport</a></li>
                        <li><a href="blog.php?category=social" class="<?= $category === 'social' ? 'active' : '' ?>">Social</a></li>
                        <li><a href="blog.php">All Categories</a></li>
                    </ul>
                </div>
            </aside>
            <div id="articles">
                <div class="category-filter">
                    <a href="blog.php" class="<?= !$category ? 'active' : '' ?>">All Posts</a>
                    <a href="blog.php?category=political" class="<?= $category === 'political' ? 'active' : '' ?>">Political</a>
                    <a href="blog.php?category=sport" class="<?= $category === 'sport' ? 'active' : '' ?>">Sport</a>
                    <a href="blog.php?category=social" class="<?= $category === 'social' ? 'active' : '' ?>">Social</a>
                </div>

                <h1 style="margin-bottom: 20px;">
                    <?= $category ? ucfirst($category) . ' Posts' : 'All Blog Posts' ?>
                </h1>

                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                    <article>
                        <div class="caption">
                            <h3><?= htmlspecialchars($post['title']) ?></h3>
                            <ul class="post-meta">
                                <li>📅 <?= date('Y-m-d', strtotime($post['created_at'])) ?></li>
                                <li>👁️ <?= $post['views'] ?> views</li>
                                <li>🏷️ <?= ucfirst($post['category']) ?></li>
                            </ul>
                            <p><?= substr(strip_tags($post['content']), 0, 200) ?>...</p>
                            <a href="post.php?id=<?= $post['id'] ?>" class="read-more">Read More →</a>
                        </div>
                        <?php if ($post['image']): ?>
                        <div class="image">
                            <img src="./assets/images/<?= basename($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" onerror="this.src='./assets/images/default.jpg'">
                        </div>
                        <?php endif; ?>
                        <div class="clearfix"></div>
                    </article>
                    <?php endforeach; ?>

                    <?php if (!$category): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>">← Previous</a>
                        <?php endif; ?>
                        <span>Page <?= $page ?></span>
                        <?php if (count($posts) === $perPage): ?>
                            <a href="?page=<?= $page + 1 ?>">Next →</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                <?php else: ?>
                    <article>
                        <div class="caption">
                            <h3>No Posts Found</h3>
                            <p>There are no blog posts in this category at the moment.</p>
                        </div>
                    </article>
                <?php endif; ?>
            </div>
            <div class="clearfix"></div>
        </section>
        <footer>
            <p><?= $settings['footer'] ?? 'Copyright 2024 Gitmag Website. All rights reserved.' ?></p>
        </footer>
    </main>
</body>
</html>