<?php
require_once "./autoload.php";

use classes\Repository\SettingRepository;
use classes\Repository\PostRepository;
use classes\StorageTypes\MySQL;

$settingRepository = new SettingRepository(new MySQL());
$postRepository = new PostRepository(new MySQL());

$settings = $settingRepository->getSettings();
$topPosts = $postRepository->getTopPosts(7);

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - <?= $settings['title'] ?? 'Gitmag website' ?></title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        .contact-container {
            display: flex;
            gap: 40px;
            margin: 20px 0;
        }

        .contact-form {
            flex: 1;
        }

        .contact-info {
            flex: 1;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .form-group textarea {
            height: 150px;
            resize: vertical;
        }

        .submit-btn {
            background: #f38b8b;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        .submit-btn:hover {
            background: #e07b7b;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        .contact-info h3 {
            color: #f38b8b;
            margin-bottom: 15px;
        }

        .contact-detail {
            margin-bottom: 15px;
            padding-left: 20px;
        }

        .contact-detail strong {
            color: #333;
        }
    </style>
</head>

<body>
    <main>
        <header>
            <h1><?= $settings['title'] ?? 'Gitmag website' ?></h1>
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
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
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
                        <h1>Contact Us</h1>
                        <p>We'd love to hear from you. Please fill out the form below and we'll get back to you as soon as possible.</p>

                        <?php if ($success): ?>
                            <div class="success-message">
                                ✅ Thank you for your message! We'll get back to you within 24 hours.
                            </div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                            <div class="error-message">
                                ❌ <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <div class="contact-container">
                            <div class="contact-form">
                                <form method="post">
                                    <div class="form-group">
                                        <label for="name">Your Name *</label>
                                        <input type="text" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Your Email *</label>
                                        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="subject">Subject *</label>
                                        <input type="text" id="subject" name="subject" required value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="message">Message *</label>
                                        <textarea id="message" name="message" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                                    </div>

                                    <button type="submit" class="submit-btn">Send Message</button>
                                </form>
                            </div>

                            <div class="contact-info">
                                <h3>Get in Touch</h3>

                                <div class="contact-detail">
                                    <strong>📧 Email</strong><br>
                                    <a href="mailto:info@gitmag.com">info@gitmag.com</a>
                                </div>

                                <div class="contact-detail">
                                    <strong>📞 Phone</strong><br>
                                    <a href="tel:+15551234567">+1 (555) 123-4567</a>
                                </div>

                                <div class="contact-detail">
                                    <strong>🏢 Address</strong><br>
                                    123 Blog Street<br>
                                    Digital City, DC 12345<br>
                                    United States
                                </div>

                                <div class="contact-detail">
                                    <strong>🕒 Business Hours</strong><br>
                                    Monday - Friday: 9:00 AM - 6:00 PM<br>
                                    Saturday: 10:00 AM - 4:00 PM<br>
                                    Sunday: Closed
                                </div>
                            </div>
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