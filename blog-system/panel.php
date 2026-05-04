<?php
require_once "./includes/auth.php";
require_once "./autoload.php";

use classes\Repository\PostRepository;
use classes\Repository\UserRepository;
use classes\StorageTypes\MySQL;

$postRepository = new PostRepository(new MySQL());
$userRepository = new UserRepository(new MySQL());

// ── Stats ─────────────────────────────────────────────────────────
$totalPosts   = $postRepository->countAll();
$totalViews   = $postRepository->sumField('views');
$publishedCnt = $postRepository->countFiltered('published', null);
$draftCnt     = $postRepository->countFiltered('draft', null);
$totalUsers   = $userRepository->countUsers();

// ── Filters ───────────────────────────────────────────────────────
$page         = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;
$perPage      = 10;
$statusFilter = trim($_GET['status'] ?? '');
$search       = trim($_GET['search'] ?? '');

$posts        = $postRepository->getFiltered(
    $statusFilter !== '' ? $statusFilter : null,
    $search       !== '' ? $search       : null,
    $page,
    $perPage
);
$totalFiltered = $postRepository->countFiltered(
    $statusFilter !== '' ? $statusFilter : null,
    $search       !== '' ? $search       : null
);

// ── Delete ────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $postRepository->delete((int)$_POST['delete_id']);
    header("Location: panel.php");
    exit;
}

// ── Status message ────────────────────────────────────────────────
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

<!-- ── Stats Cards ── -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(52,152,219,.15);color:#3498db;">📝</div>
        <div class="stat-info">
            <div class="stat-value"><?= $totalPosts ?></div>
            <div class="stat-label">Total Posts</div>
        </div>
        <div class="stat-bar" style="background:#3498db;"></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(39,174,96,.15);color:#27ae60;">✅</div>
        <div class="stat-info">
            <div class="stat-value"><?= $publishedCnt ?></div>
            <div class="stat-label">Published</div>
        </div>
        <div class="stat-bar" style="background:#27ae60;"></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(243,156,18,.15);color:#f39c12;">📄</div>
        <div class="stat-info">
            <div class="stat-value"><?= $draftCnt ?></div>
            <div class="stat-label">Drafts</div>
        </div>
        <div class="stat-bar" style="background:#f39c12;"></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(155,89,182,.15);color:#9b59b6;">👁</div>
        <div class="stat-info">
            <div class="stat-value"><?= number_format($totalViews) ?></div>
            <div class="stat-label">Total Views</div>
        </div>
        <div class="stat-bar" style="background:#9b59b6;"></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(231,76,60,.15);color:#e74c3c;">👥</div>
        <div class="stat-info">
            <div class="stat-value"><?= $totalUsers ?></div>
            <div class="stat-label">Users</div>
        </div>
        <div class="stat-bar" style="background:#e74c3c;"></div>
    </div>
</div>

<!-- ── Posts Table ── -->
<div class="card">
    <div class="card-header">
        <h2 data-i18n="dashboard">All Posts</h2>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="create.php" class="btn btn-success btn-sm" data-i18n="createNewPost">➕ Create New Post</a>
            <a href="index.php" target="_blank" class="btn btn-primary btn-sm" data-i18n="viewWebsite">👁 View Website</a>
        </div>
    </div>

    <!-- Filter bar -->
    <form method="get" class="filter-bar">
        <input type="text" name="search" class="form-control search-input"
               placeholder="Search by title…"
               value="<?= htmlspecialchars($search) ?>">
        <select name="status" class="form-control" style="width:auto;">
            <option value="">All Statuses</option>
            <option value="published" <?= $statusFilter === 'published' ? 'selected' : '' ?>>Published</option>
            <option value="draft"     <?= $statusFilter === 'draft'     ? 'selected' : '' ?>>Draft</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">🔍 Filter</button>
        <?php if ($statusFilter !== '' || $search !== ''): ?>
            <a href="panel.php" class="btn btn-secondary btn-sm">✕ Clear</a>
        <?php endif; ?>
    </form>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th data-i18n="thumbnail">Image</th>
                    <th data-i18n="title">Title</th>
                    <th data-i18n="category">Category</th>
                    <th data-i18n="status">Status</th>
                    <th data-i18n="views">Views</th>
                    <th data-i18n="date">Date</th>
                    <th data-i18n="actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td>
                                <?php if (!empty($post['image'])): ?>
                                    <img src="assets/images/<?= htmlspecialchars(basename($post['image'])) ?>"
                                         alt="<?= htmlspecialchars($post['title']) ?>"
                                         class="post-thumb"
                                         onerror="this.parentNode.innerHTML='<div class=\'thumb-placeholder\'>📷</div>'">
                                <?php else: ?>
                                    <div class="thumb-placeholder">📷</div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($post['title']) ?></strong></td>
                            <td>
                                <span class="badge badge-<?= htmlspecialchars($post['category']) ?>">
                                    <?= ucfirst(htmlspecialchars($post['category'])) ?>
                                </span>
                            </td>
                            <td>
                                <?php $st = $post['status'] ?? 'published'; ?>
                                <span class="badge badge-<?= htmlspecialchars($st) ?>"
                                      data-i18n="<?= htmlspecialchars($st) ?>">
                                    <?= ucfirst(htmlspecialchars($st)) ?>
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
                        <td colspan="7" style="text-align:center;padding:30px;color:var(--text-muted);">
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
        <?php
        $queryBase = '?';
        if ($statusFilter !== '') $queryBase .= 'status=' . urlencode($statusFilter) . '&';
        if ($search       !== '') $queryBase .= 'search=' . urlencode($search) . '&';
        ?>
        <?php if ($page > 1): ?>
            <a href="<?= $queryBase ?>page=<?= $page - 1 ?>" class="btn btn-secondary btn-sm"
               data-i18n="previous">← Previous</a>
        <?php endif; ?>
        <span class="pagination-info">
            <span data-i18n="page">Page</span> <?= $page ?>
            (<?= $totalFiltered ?> results)
        </span>
        <?php if (count($posts) === $perPage && ($page * $perPage) < $totalFiltered): ?>
            <a href="<?= $queryBase ?>page=<?= $page + 1 ?>" class="btn btn-secondary btn-sm"
               data-i18n="next">Next →</a>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/admin_layout_end.php'; ?>

