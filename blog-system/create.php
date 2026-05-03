<?php
require_once "./includes/auth.php";
require_once "./autoload.php";

use classes\Post;
use classes\Repository\PostRepository;
use classes\StorageTypes\MySQL;

$postRepository = new PostRepository(new MySQL());

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title']    ?? '');
    $content  = trim($_POST['content']  ?? '');
    $category = trim($_POST['category'] ?? 'political');

    $image = handleImageUpload();

    if ($title === '' || $content === '') {
        $error = "Title and content are required.";
    } else {
        $post = new Post(null, $title, $content, $category, $postRepository, $image);

        if ($post->save()) {
            header("Location: panel.php?message=success");
            exit;
        } else {
            $error = "Failed to create post. Please try again.";
        }
    }
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

    $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
    $imagePath = $uploadDir . $imageName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
        return $imageName;
    }

    return null;
}

$pageTitle   = 'Create Post';
$currentPage = 'create';
include 'includes/admin_layout_start.php';
?>

<div class="card">
    <div class="card-header">
        <h2 data-i18n="createTitle">Create New Post</h2>
        <a href="panel.php" class="btn btn-secondary btn-sm" data-i18n="backBtn">← Back to Dashboard</a>
    </div>
    <div class="card-body">

        <?php if ($error): ?>
            <div class="alert alert-danger">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title" data-i18n="postTitle">Post Title *</label>
                <input type="text" id="title" name="title" class="form-control" required
                       value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                       placeholder="Enter post title">
            </div>

            <div class="form-group">
                <label for="category" data-i18n="postCategory">Category *</label>
                <select id="category" name="category" class="form-control" required>
                    <option value="political" <?= ($_POST['category'] ?? '') === 'political' ? 'selected' : '' ?>
                            data-i18n="political">Political</option>
                    <option value="sport" <?= ($_POST['category'] ?? '') === 'sport' ? 'selected' : '' ?>
                            data-i18n="sport">Sport</option>
                    <option value="social" <?= ($_POST['category'] ?? '') === 'social' ? 'selected' : '' ?>
                            data-i18n="social">Social</option>
                </select>
            </div>

            <div class="form-group">
                <label for="content" data-i18n="postContent">Content *</label>
                <textarea id="content" name="content" class="form-control" rows="12" required
                          placeholder="Write your post content here..."><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="image" data-i18n="featuredImage">Featured Image</label>
                <input type="file" id="image" name="image" class="form-control" accept="image/*">
                <div class="form-hint">Optional: JPG, PNG, GIF</div>
            </div>

            <div style="display:flex;gap:10px;justify-content:center;margin-top:24px;flex-wrap:wrap;">
                <button type="submit" class="btn btn-success" data-i18n="createBtn">📝 Create Post</button>
                <a href="panel.php" class="btn btn-secondary" data-i18n="backBtn">← Back to Dashboard</a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/admin_layout_end.php'; ?>
