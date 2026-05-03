<?php
require_once "./autoload.php";

use classes\Repository\PostRepository;
use classes\Repository\SettingRepository;
use classes\StorageTypes\MySQL;

$postRepository = new PostRepository(new MySQL());
$settingRepository = new SettingRepository(new MySQL());

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 5;
$posts = $postRepository->getAllPaginated($page, $perPage);
$settings = $settingRepository->getSettings();
$topPosts = $postRepository->getTopPosts(7);
$latestPosts = $postRepository->getAllPaginated(1, 7);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $settings['description'] ?? 'Gitmag website' ?>">
    <meta name="keywords" content="<?= $settings['keywords'] ?? 'gitmag, video, programming' ?>">
    <meta name="author" content="<?= $settings['author'] ?? 'sohrab azinfar' ?>">
    <title><?= $settings['title'] ?? 'Gitmag website' ?></title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
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
                <input type="text" name="search" placeholder="Search your word" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
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
                    <h2>Last Posts</h2>
                    <ul>
                        <?php foreach ($latestPosts as $post): ?>
                        <li><a href="post.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="aside-box">
                    <h2>Categories</h2>
                    <ul>
                        <li><a href="blog.php?category=political">Political</a></li>
                        <li><a href="blog.php?category=sport">Sport</a></li>
                        <li><a href="blog.php?category=social">Social</a></li>
                    </ul>
                </div>
            </aside>
            <div id="articles">
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

                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>">← Previous</a>
                        <?php endif; ?>
                        <span>Page <?= $page ?></span>
                        <?php if (count($posts) === $perPage): ?>
                            <a href="?page=<?= $page + 1 ?>">Next →</a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <article>
                        <div class="caption">
                            <h3>No Posts Found</h3>
                            <p>There are no blog posts available at the moment.</p>
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