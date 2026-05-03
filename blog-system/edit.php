<?php
require_once "./includes/auth.php";
require_once "./autoload.php";

use classes\Post;
use classes\Repository\PostRepository;
use classes\StorageTypes\MySQL;

$postRepository = new PostRepository(new MySQL());

$postId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;

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
    $title    = trim($_POST['title']    ?? '');
    $content  = trim($_POST['content']  ?? '');
    $category = trim($_POST['category'] ?? 'political');

    $image = handleImageUpload($postData['image']);

    if ($title === '' || $content === '') {
        $error = "Title and content are required.";
    } else {
        $post = new Post($postId, $title, $content, $category, $postRepository, $image);

        if ($post->save()) {
            header("Location: panel.php?message=updated");
            exit;
        } else {
            $error = "Failed to update post. Please try again.";
        }
    }
}

function handleImageUpload(?string $currentImage): ?string
{
    $image = $currentImage;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'assets/images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
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

$pageTitle   = 'Edit Post';
$currentPage = 'edit';
include 'includes/admin_layout_start.php';
?>

<div class="card">
    <div class="card-header">
        <h2 data-i18n="editTitle">Edit Post</h2>
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
                       value="<?= htmlspecialchars($_POST['title'] ?? $postData['title']) ?>"
                       placeholder="Enter post title">
            </div>

            <div class="form-group">
                <label for="category" data-i18n="postCategory">Category *</label>
                <select id="category" name="category" class="form-control" required>
                    <?php $selCat = $_POST['category'] ?? $postData['category']; ?>
                    <option value="political" <?= $selCat === 'political' ? 'selected' : '' ?>
                            data-i18n="political">Political</option>
                    <option value="sport" <?= $selCat === 'sport' ? 'selected' : '' ?>
                            data-i18n="sport">Sport</option>
                    <option value="social" <?= $selCat === 'social' ? 'selected' : '' ?>
                            data-i18n="social">Social</option>
                </select>
            </div>

            <div class="form-group">
                <label for="content" data-i18n="postContent">Content *</label>
                <textarea id="content" name="content" class="form-control" rows="12" required><?= htmlspecialchars($_POST['content'] ?? $postData['content']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="image" data-i18n="featuredImage">Featured Image</label>
                <?php if (!empty($postData['image'])): ?>
                    <div style="margin-bottom:10px;text-align:center;">
                        <img src="assets/images/<?= htmlspecialchars($postData['image']) ?>"
                             alt="Current post image"
                             onerror="this.style.display='none'"
                             style="max-width:280px;border-radius:8px;box-shadow:var(--shadow-sm);">
                    </div>
                <?php endif; ?>
                <input type="file" id="image" name="image" class="form-control" accept="image/*">
                <div class="form-hint">Choose a new image to replace the current one (optional)</div>
            </div>

            <div style="display:flex;gap:10px;justify-content:center;margin-top:24px;flex-wrap:wrap;">
                <button type="submit" class="btn btn-success" data-i18n="updateBtn">💾 Update Post</button>
                <a href="panel.php" class="btn btn-secondary" data-i18n="backBtn">← Back to Dashboard</a>
                <a href="post.php?id=<?= $postId ?>" target="_blank" class="btn btn-primary" data-i18n="viewPostBtn">👁 View Post</a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/admin_layout_end.php'; ?>
