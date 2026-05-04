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
    $status   = in_array($_POST['status'] ?? '', ['published', 'draft'], true)
                    ? $_POST['status']
                    : 'published';

    $image = handleImageUpload();

    if ($title === '' || $content === '') {
        $error = "Title and content are required.";
    } else {
        $post = new Post(null, $title, $content, $category, $postRepository, $image, $status);

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

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExtensions, true)) {
        return null;
    }

    $uploadDir = 'assets/images/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $imageName = 'post_' . bin2hex(random_bytes(16)) . '.' . $ext;
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

<!-- Load Quill CSS from CDN -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">

<div class="card">
    <div class="card-header">
        <h2 data-i18n="createTitle">Create New Post</h2>
        <a href="panel.php" class="btn btn-secondary btn-sm" data-i18n="backBtn">← Back to Dashboard</a>
    </div>
    <div class="card-body">

        <?php if ($error): ?>
            <div class="alert alert-danger">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" id="postForm">

            <!-- Title -->
            <div class="form-group">
                <label for="title" data-i18n="postTitle">Post Title *</label>
                <input type="text" id="title" name="title" class="form-control" required
                       value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                       placeholder="Enter post title">
            </div>

            <!-- Category + Status row -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
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
                    <label for="status" data-i18n="postStatus">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="published" <?= ($_POST['status'] ?? 'published') === 'published' ? 'selected' : '' ?>
                                data-i18n="published">Published</option>
                        <option value="draft" <?= ($_POST['status'] ?? '') === 'draft' ? 'selected' : '' ?>
                                data-i18n="draft">Draft</option>
                    </select>
                </div>
            </div>

            <!-- Rich-text content -->
            <div class="form-group">
                <label data-i18n="postContent">Content *</label>
                <!-- Visible Quill editor -->
                <div id="quillEditor" style="min-height:260px;"></div>
                <!-- Hidden textarea receives HTML before submit -->
                <textarea id="content" name="content" style="display:none;"
                          required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
            </div>

            <!-- Featured image with live preview -->
            <div class="form-group">
                <label data-i18n="featuredImage">Featured Image</label>
                <input type="file" id="image" name="image" class="form-control"
                       accept="image/jpeg,image/png,image/gif,image/webp"
                       onchange="previewImage(this,'imgPreview','imgPreviewWrap')">
                <div class="form-hint">Optional · JPG, PNG, GIF, WebP</div>
                <div class="image-preview-container" id="imgPreviewWrap" style="display:none;">
                    <img id="imgPreview" src="" alt="Preview" class="image-preview">
                    <button type="button" class="btn btn-secondary btn-sm"
                            onclick="clearImage('image','imgPreview','imgPreviewWrap')">✕ Remove</button>
                </div>
            </div>

            <div style="display:flex;gap:10px;justify-content:center;margin-top:24px;flex-wrap:wrap;">
                <button type="submit" class="btn btn-success" data-i18n="createBtn">📝 Create Post</button>
                <a href="panel.php" class="btn btn-secondary" data-i18n="backBtn">← Back to Dashboard</a>
            </div>
        </form>
    </div>
</div>

<!-- Quill JS -->
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
// ── Quill init ────────────────────────────────────────────────────
const quill = new Quill('#quillEditor', {
    theme: 'snow',
    placeholder: 'Write your post content here…',
    modules: {
        toolbar: [
            [{ header: [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ color: [] }, { background: [] }],
            [{ list: 'ordered' }, { list: 'bullet' }],
            [{ align: [] }],
            ['blockquote', 'code-block'],
            ['link', 'image'],
            ['clean']
        ]
    }
});

// Populate editor with any submitted content (on validation failure redirect)
const existingContent = document.getElementById('content').value;
if (existingContent) {
    quill.clipboard.dangerouslyPasteHTML(existingContent);
}

// Before submit, copy Quill HTML into the hidden textarea
document.getElementById('postForm').addEventListener('submit', function () {
    document.getElementById('content').value = quill.root.innerHTML;
});

// ── Live image preview ────────────────────────────────────────────
function previewImage(input, previewId, wrapId) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (e) {
        document.getElementById(previewId).src = e.target.result;
        document.getElementById(wrapId).style.display = 'flex';
    };
    reader.readAsDataURL(file);
}

function clearImage(inputId, previewId, wrapId) {
    document.getElementById(inputId).value = '';
    document.getElementById(previewId).src  = '';
    document.getElementById(wrapId).style.display = 'none';
}
</script>

<?php include 'includes/admin_layout_end.php'; ?>

