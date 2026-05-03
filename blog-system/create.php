<?php
require_once "./autoload.php";

use classes\Post;
use classes\Repository\PostRepository;
use classes\StorageTypes\MySQL;

$postRepository = new PostRepository(new MySQL());

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = getPostValue('title');
    $content = getPostValue('content');
    $category = getPostValue('category', 'political');

    $image = handleImageUpload();

    if (empty($title) || empty($content)) {
        $error = "Title and content are required.";
    } else {
        $post = new Post(null, $title, $content, $category, $image, $postRepository);

        if ($post->save()) {
            header("Location: panel.php?message=success");
            exit;
        } else {
            $error = "Failed to create post. Please try again.";
        }
    }
}

function getPostValue(string $key, string $default = ''): string
{
    return trim($_POST[$key] ?? $default);
}

function handleImageUpload(): ?string
{
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $uploadDir = 'assets/images/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $imageName = uniqid() . '_' . $_FILES['image']['name'];
    $imagePath = $uploadDir . $imageName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
        return $imageName;
    }

    return null;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post - Admin Panel</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/panel.css">
    <style>
        .admin-header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            margin: -20px -20px 20px -20px;
            border-radius: 8px 8px 0 0;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        .form-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            margin: 5px;
        }

        .btn-success {
            background: #27ae60;
        }

        .btn-secondary {
            background: #95a5a6;
        }
    </style>
</head>

<body>
    <main>
        <nav>
            <ul>
                <li><a href="./panel.php">📊 Dashboard</a></li>
                <li><a href="./create.php">➕ Create Post</a></li>
                <li><a href="./index.php">🏠 View Site</a></li>
            </ul>
        </nav>
        <section class="content">
            <div class="admin-header">
                <h1>➕ Create New Post</h1>
                <p>Add a new blog post to your website</p>
            </div>

            <div class="form-container">
                <?php if ($error): ?>
                    <div class="error-message">❌ <?= $error ?></div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Post Title *</label>
                        <input type="text" id="title" name="title" required
                            value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                            placeholder="Enter post title">
                    </div>

                    <div class="form-group">
                        <label for="category">Category *</label>
                        <select id="category" name="category" required>
                            <option value="political" <?= ($_POST['category'] ?? '') === 'political' ? 'selected' : '' ?>>Political</option>
                            <option value="sport" <?= ($_POST['category'] ?? '') === 'sport' ? 'selected' : '' ?>>Sport</option>
                            <option value="social" <?= ($_POST['category'] ?? '') === 'social' ? 'selected' : '' ?>>Social</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="content">Content *</label>
                        <textarea id="content" name="content" rows="15" required
                            placeholder="Write your post content here..."><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="image">Featured Image</label>
                        <input type="file" id="image" name="image" accept="image/*">
                        <small>Optional: Upload a featured image for your post (JPG, PNG, GIF)</small>
                    </div>

                    <div style="text-align: center; margin-top: 30px;">
                        <button type="submit" class="btn btn-success">📝 Create Post</button>
                        <a href="./panel.php" class="btn btn-secondary">← Back to Dashboard</a>
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>

</html>