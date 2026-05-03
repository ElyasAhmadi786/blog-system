<?php
require_once "./includes/auth.php";
require_once "./autoload.php";

use classes\Repository\PostRepository;
use classes\StorageTypes\MySQL;

$postRepository = new PostRepository(new MySQL());

$page    = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$posts   = $postRepository->getAllPaginated($page, $perPage);

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $postRepository->delete((int)$_POST['delete_id']);
    header("Location: panel.php");
    exit;
}

// Status message
$message = null;
if (isset($_GET['message'])) {
    $msgs = [
        'success' => ['type' => 'success', 'text' => '✅ Post created successfully!'],
        'updated' => ['type' => 'success', 'text' => '✅ Post updated successfully!'],
    ];
    $message = $msgs[$_GET['message']] ?? null;
}

$pageTitle   = 'Dashboard';
$currentPage = 'dashboard';
include 'includes/admin_layout_start.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?= htmlspecialchars($message['type']) ?>"><?= $message['text'] ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2 data-i18n="dashboard">Dashboard</h2>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="create.php" class="btn btn-success btn-sm" data-i18n="createNewPost">➕ Create New Post</a>
            <a href="index.php" target="_blank" class="btn btn-primary btn-sm" data-i18n="viewWebsite">👁 View Website</a>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th data-i18n="id">ID</th>
                    <th data-i18n="title">Title</th>
                    <th data-i18n="category">Category</th>
                    <th data-i18n="views">Views</th>
                    <th data-i18n="date">Date</th>
                    <th data-i18n="actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?= (int)$post['id'] ?></td>
                            <td><strong><?= htmlspecialchars($post['title']) ?></strong></td>
                            <td>
                                <span class="badge badge-<?= htmlspecialchars($post['category']) ?>"
                                      data-i18n="<?= htmlspecialchars($post['category']) ?>">
                                    <?= ucfirst(htmlspecialchars($post['category'])) ?>
                                </span>
                            </td>
                            <td>👁 <?= (int)$post['views'] ?></td>
                            <td><?= date('Y-m-d', strtotime($post['created_at'])) ?></td>
                            <td>
                                <div class="td-actions">
                                    <a href="edit.php?id=<?= (int)$post['id'] ?>"
                                       class="btn btn-primary btn-sm" data-i18n="edit">✏️ Edit</a>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="delete_id" value="<?= (int)$post['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm"
                                                data-i18n="delete"
                                                onclick="return confirm(getDeleteConfirm())">
                                            🗑 Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center;padding:30px;color:var(--text-muted);">
                            📝 <span data-i18n="noPosts">No posts found.</span>
                            <a href="create.php" style="color:var(--accent);margin-left:6px;"
                               data-i18n="createFirst">Create your first post</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" class="btn btn-secondary btn-sm" data-i18n="previous">← Previous</a>
        <?php endif; ?>
        <span class="pagination-info">
            <span data-i18n="page">Page</span> <?= $page ?>
        </span>
        <?php if (count($posts) === $perPage): ?>
            <a href="?page=<?= $page + 1 ?>" class="btn btn-secondary btn-sm" data-i18n="next">Next →</a>
        <?php endif; ?>
    </div>
</div>

<script>
function getDeleteConfirm() {
    const lang = localStorage.getItem('admin_lang') || 'en';
    const msgs = {
        en: 'Are you sure you want to delete this post?',
        fa: '\u0622\u06CC\u0627 \u0645\u0637\u0645\u0626\u0646\u06CC\u062F \u06A9\u0647 \u0645\u06CC\u200C\u062E\u0648\u0627\u0647\u06CC\u062F \u0627\u06CC\u0646 \u067E\u0633\u062A \u0631\u0627 \u062D\u0630\u0641 \u06A9\u0646\u06CC\u062F\u061F'
    };
    return msgs[lang] || msgs.en;
}
</script>

<?php include 'includes/admin_layout_end.php'; ?>
