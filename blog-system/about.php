<?php
require_once "./autoload.php";

use classes\Repository\SettingRepository;
use classes\Repository\PostRepository;
use classes\StorageTypes\MySQL;

$settingRepository = new SettingRepository(new MySQL());
$postRepository = new PostRepository(new MySQL());

$settings = $settingRepository->getSettings();
$topPosts = $postRepository->getTopPosts(7);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - <?= $settings['title'] ?? 'Gitmag website' ?></title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        .about-content {
            line-height: 1.6;
        }
        .about-content h2 {
            color: #f38b8b;
            margin: 20px 0 10px 0;
        }
        .team-members {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin: 20px 0;
        }
        .team-member {
            flex: 1;
            min-width: 200px;
            text-align: center;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 8px;
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
                <a href="./login.php">Admin Panel</a>
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
            </aside>
            <div id="articles">
                <article>
                    <div class="caption about-content">
                        <h1>About Us</h1>
                        
                        <h2>Our Story</h2>
                        <p>Welcome to <?= $settings['title'] ?? 'Gitmag website' ?>, your premier destination for engaging content across various categories including politics, sports, and social issues. Founded in 2024, we are committed to delivering high-quality, informative, and thought-provoking articles to our readers.</p>
                        
                        <h2>Our Mission</h2>
                        <p>Our mission is to provide a platform where diverse voices can be heard and important stories can be shared. We believe in the power of information to inspire change and foster understanding in our community.</p>
                        
                        <h2>Our Team</h2>
                        <div class="team-members">
                            <div class="team-member">
                                <h3>Elyas Ahmadi</h3>
                                <p>Founder & Editor-in-Chief</p>
                            </div>
                            <div class="team-member">
                                <h3>John Doe</h3>
                                <p>Senior Writer</p>
                            </div>
                            <div class="team-member">
                                <h3>Jane Smith</h3>
                                <p>Content Manager</p>
                            </div>
                        </div>
                        
                        <h2>What We Offer</h2>
                        <ul>
                            <li>📰 Latest news and updates</li>
                            <li>🎯 In-depth analysis</li>
                            <li>📊 Expert opinions</li>
                            <li>📸 Visual storytelling</li>
                            <li>💬 Community engagement</li>
                        </ul>
                        
                        <h2>Contact Information</h2>
                        <p>Email: elyasahmadi@gmail.com<br>
                        Phone: +93 (93) 796-504564<br>
                        Address: 123 Blog Street, Digital City</p>
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