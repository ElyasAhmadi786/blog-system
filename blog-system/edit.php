<?php
require_once "./autoload.php";

use classes\Post;
use classes\Repository\PostRepository;
use classes\StorageTypes\MySQL;

$postRepository = new PostRepository(new MySQL());
$postId = $_GET['id'] ?? null;

if (!$postId) {
    header("Location: panel.php");
    exit;
}

$postData = $postRepository->getById($postId);

if (!$postData) {
    header("Location: panel.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = getPostValue('title');
    $content = getPostValue('content');
    $category = getPostValue('category', 'political');

    $image = handleImageUpload($postData['image']);

    if (empty($title) || empty($content)) {
        $error = "Title and content are required.";
    } else {
        $post = new Post($postId, $title, $content, $category, $image, $postRepository);

        if ($post->save()) {
            header("Location: panel.php?message=updated");
            exit;
        } else {
            $error = "Failed to update post. Please try again.";
        }
    }
}

function getPostValue(string $key, string $default = ''): string
{
    return trim($_POST[$key] ?? $default);
}

function handleImageUpload(?string $currentImage): ?string
{
    $image = $currentImage;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'assets/images/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageName = uniqid() . '_' . $_FILES['image']['name'];
        $imagePath = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            if ($currentImage && file_exists($uploadDir . $currentImage)) {
                unlink($uploadDir . $currentImage);
            }
            $image = $imageName;
        }
    }

    return $image;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post - Admin Panel</title>
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

        .current-image {
            text-align: center;
            margin: 15px 0;
        }

        .current-image img {
            max-width: 300px;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
                <h1>✏️ Edit Post</h1>
                <p>Update your blog post content</p>
            </div>

            <div class="form-container">
                <?php if ($error): ?>
                    <div class="error-message">❌ <?= $error ?></div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Post Title *</label>
                        <input type="text" id="title" name="title" required
                            value="<?= htmlspecialchars($postData['title']) ?>"
                            placeholder="Enter post title">
                    </div>

                    <div class="form-group">
                        <label for="category">Category *</label>
                        <select id="category" name="category" required>
                            <option value="political" <?= $postData['category'] === 'political' ? 'selected' : '' ?>>Political</option>
                            <option value="sport" <?= $postData['category'] === 'sport' ? 'selected' : '' ?>>Sport</option>
                            <option value="social" <?= $postData['category'] === 'social' ? 'selected' : '' ?>>Social</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="content">Content *</label>
                        <textarea id="content" name="content" rows="15" required
                            placeholder="Write your post content here..."><?= htmlspecialchars($postData['content']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="image">Featured Image</label>

                        <?php if ($postData['image']): ?>
                            <div class="current-image">
                                <p><strong>Current Image:</strong></p>
                                <img src="assets/images/<?= $postData['image'] ?>"
                                    alt="Current post image"
                                    onerror="this.src='assets/images/default.jpg'">
                            </div>
                        <?php endif; ?>

                        <input type="file" id="image" name="image" accept="image/*">
                        <small>Choose a new image to replace the current one (optional)</small>
                    </div>

                    <div style="text-align: center; margin-top: 30px;">
                        <button type="submit" class="btn btn-success">💾 Update Post</button>
                        <a href="./panel.php" class="btn btn-secondary">← Back to Dashboard</a>
                        <a href="./post.php?id=<?= $postId ?>" class="btn">👁️ View Post</a>
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>

</html>