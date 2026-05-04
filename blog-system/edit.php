<?php
require_once "./includes/auth.php";
require_once "./autoload.php";

use classes\Post;
use classes\Repository\PostRepository;
use classes\StorageTypes\MySQL;

$postRepository = new PostRepository(new MySQL());

$postId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

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
    $status   = in_array($_POST['status'] ?? '', ['published', 'draft'], true)
                    ? $_POST['status']
                    : ($postData['status'] ?? 'published');

    $image = handleImageUpload($postData['image']);

    if ($title === '' || $content === '') {
        $error = "Title and content are required.";
    } else {
        $post = new Post($postId, $title, $content, $category, $postRepository, $image, $status);

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
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions, true)) {
            return $image;
        }

        $uploadDir = 'assets/images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $imageName = 'post_' . bin2hex(random_bytes(16)) . '.' . $ext;
        $imagePath = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            // Remove old image file if it was an uploaded one (not a seed image like "1.jpg")
            if ($currentImage && strpos($currentImage, 'post_') === 0
                && file_exists($uploadDir . $currentImage)) {
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

<!-- Load Quill CSS from CDN -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">

<div class="card">
    <div class="card-header">
        <h2 data-i18n="editTitle">Edit Post</h2>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="panel.php" class="btn btn-secondary btn-sm" data-i18n="backBtn">← Back</a>
            <a href="post.php?id=<?= $postId ?>" target="_blank" class="btn btn-primary btn-sm"
               data-i18n="viewPostBtn">👁 View Post</a>
        </div>
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
                       value="<?= htmlspecialchars($_POST['title'] ?? $postData['title']) ?>"
                       placeholder="Enter post title">
            </div>

            <!-- Category + Status row -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                <div class="form-group">
                    <label for="category" data-i18n="postCategory">Category *</label>
                    <select id="category" name="category" class="form-control" required>
                        <?php $selCat = $_POST['category'] ?? $postData['category']; ?>
                        <option value="political" <?= $selCat === 'political' ? 'selected' : '' ?>
                                data-i18n="political">Political</option>
                        <option value="sport"     <?= $selCat === 'sport'     ? 'selected' : '' ?>
                                data-i18n="sport">Sport</option>
                        <option value="social"    <?= $selCat === 'social'    ? 'selected' : '' ?>
                                data-i18n="social">Social</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status" data-i18n="postStatus">Status</label>
                    <select id="status" name="status" class="form-control">
                        <?php $selSt = $_POST['status'] ?? ($postData['status'] ?? 'published'); ?>
                        <option value="published" <?= $selSt === 'published' ? 'selected' : '' ?>
                                data-i18n="published">Published</option>
                        <option value="draft"     <?= $selSt === 'draft'     ? 'selected' : '' ?>
                                data-i18n="draft">Draft</option>
                    </select>
                </div>
            </div>

            <!-- Rich-text content -->
            <div class="form-group">
                <label data-i18n="postContent">Content *</label>
                <div id="quillEditor" style="min-height:260px;"></div>
                <textarea id="content" name="content" style="display:none;"
                          required><?= htmlspecialchars($_POST['content'] ?? $postData['content']) ?></textarea>
            </div>

            <!-- Featured image with live preview -->
            <div class="form-group">
                <label data-i18n="featuredImage">Featured Image</label>

                <?php if (!empty($postData['image'])): ?>
                    <!-- Current image always visible until replaced -->
                    <div class="image-preview-container" id="currentImgWrap">
                        <img src="assets/images/<?= htmlspecialchars(basename($postData['image'])) ?>"
                             alt="Current image"
                             class="image-preview"
                             id="currentImg"
                             onerror="this.parentNode.style.display='none'">
                        <span style="font-size:12px;color:var(--text-muted);">Current image</span>
                    </div>
                <?php endif; ?>

                <input type="file" id="image" name="image" class="form-control"
                       accept="image/jpeg,image/png,image/gif,image/webp"
                       style="margin-top:10px;"
                       onchange="previewImage(this,'imgPreview','imgPreviewWrap','currentImgWrap')">
                <div class="form-hint">Choose a new image to replace the current one (optional)</div>

                <div class="image-preview-container" id="imgPreviewWrap" style="display:none;">
                    <img id="imgPreview" src="" alt="New image preview" class="image-preview">
                    <button type="button" class="btn btn-secondary btn-sm"
                            onclick="clearImage('image','imgPreview','imgPreviewWrap','currentImgWrap')">
                        ✕ Cancel new image
                    </button>
                </div>
            </div>

            <div style="display:flex;gap:10px;justify-content:center;margin-top:24px;flex-wrap:wrap;">
                <button type="submit" class="btn btn-success" data-i18n="updateBtn">💾 Update Post</button>
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

// Seed editor with current post content
const existingContent = document.getElementById('content').value;
if (existingContent) {
    quill.clipboard.dangerouslyPasteHTML(existingContent);
}

// Copy HTML to hidden textarea before submit
document.getElementById('postForm').addEventListener('submit', function () {
    document.getElementById('content').value = quill.root.innerHTML;
});

// ── Live image preview ────────────────────────────────────────────
function previewImage(input, previewId, wrapId, currentWrapId) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (e) {
        document.getElementById(previewId).src = e.target.result;
        document.getElementById(wrapId).style.display = 'flex';
        // Hide current image to make the "new" preview the focus
        if (currentWrapId) {
            const cw = document.getElementById(currentWrapId);
            if (cw) cw.style.opacity = '0.35';
        }
    };
    reader.readAsDataURL(file);
}

function clearImage(inputId, previewId, wrapId, currentWrapId) {
    document.getElementById(inputId).value  = '';
    document.getElementById(previewId).src  = '';
    document.getElementById(wrapId).style.display = 'none';
    if (currentWrapId) {
        const cw = document.getElementById(currentWrapId);
        if (cw) cw.style.opacity = '1';
    }
}
</script>

<?php include 'includes/admin_layout_end.php'; ?>

