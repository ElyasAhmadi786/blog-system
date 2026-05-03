<?php
require_once "./autoload.php";

use classes\Repository\PostRepository;
use classes\Repository\SettingRepository;
use classes\StorageTypes\MySQL;

$postRepository = new PostRepository(new MySQL());
$settingRepository = new SettingRepository(new MySQL());

$posts = $postRepository->getAll();
$settings = $settingRepository->getSettings();
$topPosts = $postRepository->getTopPosts(7);

// Filter posts with images
$postsWithImages = array_filter($posts, function($post) {
    return !empty($post['image']);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - <?= $settings['title'] ?? 'Gitmag website' ?></title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        .gallery-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .gallery-item {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .gallery-item:hover {
            transform: translateY(-5px);
        }
        .gallery-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .gallery-content {
            padding: 15px;
        }
        .gallery-content h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 16px;
        }
        .gallery-content p {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .gallery-meta {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #999;
        }
        .view-post {
            display: inline-block;
            padding: 5px 15px;
            background: #f38b8b;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        .no-images {
            text-align: center;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 8px;
            margin: 20px 0;
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
            </aside>
            <div id="articles">
                <article>
                    <div class="caption">
                        <h1>Image Gallery</h1>
                        <p>Browse through our collection of images from various blog posts. Click on any image to read the full article.</p>
                        
                        <?php if (!empty($postsWithImages)): ?>
                            <div class="gallery-container">
                                <?php foreach ($postsWithImages as $post): ?>
                                <div class="gallery-item">
                                    <img src="./assets/images/<?= $post['image'] ?>" 
                                         alt="<?= htmlspecialchars($post['title']) ?>" 
                                         onerror="this.src='./assets/images/default.jpg'">
                                    <div class="gallery-content">
                                        <h3><?= htmlspecialchars($post['title']) ?></h3>
                                        <p><?= substr(strip_tags($post['content']), 0, 100) ?>...</p>
                                        <div class="gallery-meta">
                                            <span>👁️ <?= $post['views'] ?> views</span>
                                            <span>🏷️ <?= ucfirst($post['category']) ?></span>
                                        </div>
                                        <a href="post.php?id=<?= $post['id'] ?>" class="view-post">Read Post</a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-images">
                                <h3>📷 No Images Available</h3>
                                <p>There are no images in the gallery at the moment.</p>
                                <p><a href="panel.php" class="view-post">Go to Admin Panel to add posts with images</a></p>
                            </div>
                        <?php endif; ?>
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