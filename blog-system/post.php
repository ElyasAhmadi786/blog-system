<?php
require_once "./autoload.php";

use classes\Repository\PostRepository;
use classes\Repository\SettingRepository;
use classes\StorageTypes\MySQL;

$postRepository = new PostRepository(new MySQL());
$settingRepository = new SettingRepository(new MySQL());

$postId = $_GET['id'] ?? null;

if (!$postId) {
    header("Location: index.php");
    exit;
}

$post = $postRepository->getById($postId);
$settings = $settingRepository->getSettings();
$topPosts = $postRepository->getTopPosts(7);

if (!$post) {
    header("Location: index.php");
    exit;
}

$postRepository->update($postId, ['views' => $post['views'] + 1]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= substr(strip_tags($post['content']), 0, 150) ?>">
    <meta name="keywords" content="<?= $settings['keywords'] ?? 'gitmag, video, programming' ?>">
    <meta name="author" content="<?= $settings['author'] ?? 'sohrab azinfar' ?>">
    <title><?= htmlspecialchars($post['title']) ?> - <?= $settings['title'] ?? 'Gitmag website' ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .post-content {
            line-height: 1.8;
            font-size: 16px;
        }

        .post-content p {
            margin-bottom: 20px;
        }

        .post-image {
            text-align: center;
            margin: 20px 0;
        }

        .post-image img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .post-meta {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .admin-actions {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .btn-success {
            background: #27ae60;
        }
    </style>
</head>

<body>
    <main>
        <header>
            <h1><?= $settings['title'] ?? 'Gitmag website' ?></h1>
            <?php if ($settings['logo']): ?>
                <div id="logo">
                    <img src="assets/images/logo.png" alt="Gitmag">
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
                <a href="./panel.php">Admin Panel</a>
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
                        <?php foreach ($topPosts as $topPost): ?>
                            <li><a href="./post.php?id=<?= $topPost['id'] ?>"><?= htmlspecialchars($topPost['title']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </aside>
            <div id="articles">
                <article>
                    <div class="caption">
                        <h1><?= htmlspecialchars($post['title']) ?></h1>

                        <div class="post-meta">
                            <ul class="post-meta" style="margin: 0;">
                                <li>📅 <?= date('F j, Y', strtotime($post['created_at'])) ?></li>
                                <li>👁️ <?= $post['views'] + 1 ?> views</li>
                                <li>🏷️ <?= ucfirst($post['category']) ?></li>
                            </ul>
                        </div>

                        <?php if ($post['image']): ?>
                            <div class="post-image">
                                <img src="assets/images/<?= $post['image'] ?>"
                                    alt="<?= htmlspecialchars($post['title']) ?>"
                                    onerror="this.src='assets/images/default.jpg'">
                            </div>
                        <?php endif; ?>

                        <div class="post-content">
                            <?= nl2br(htmlspecialchars($post['content'])) ?>
                        </div>

                        <div class="admin-actions">
                            <p><strong>Admin Actions:</strong></p>
                            <a href="./edit.php?id=<?= $post['id'] ?>" class="btn">✏️ Edit This Post</a>
                            <a href="./panel.php" class="btn btn-success">📊 Manage All Posts</a>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </article>
            </div>
            <div class="clearfix"></div>
        </section>
        <footer>
            <p><?= $settings['footer'] ?? 'Copyright 2024 Gitmag Website. All rights reserved.' ?></p>
        </footer>
    </main>
</body>

</html>