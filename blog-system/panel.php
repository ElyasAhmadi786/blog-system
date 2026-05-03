<?php
require_once "./autoload.php";

use classes\Repository\PostRepository;
use classes\StorageTypes\MySQL;

$postRepository = new PostRepository(new MySQL());
$page = getCurrentPage();
$perPage = 10;
$posts = $postRepository->getAllPaginated($page, $perPage);

handleDeleteRequest($postRepository);
$message = getStatusMessage();

function getCurrentPage(): int
{
    return isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
}

function handleDeleteRequest(PostRepository $postRepository): void
{
    if (isset($_POST['delete_id'])) {
        $postRepository->delete((int)$_POST['delete_id']);
        header("Location: panel.php");
        exit;
    }
}

function getStatusMessage(): string
{
    if (!isset($_GET['message'])) {
        return '';
    }

    $messages = [
        'success' => '<div class="success-message">✅ Post created successfully!</div>',
        'updated' => '<div class="success-message">✅ Post updated successfully!</div>'
    ];

    return $messages[$_GET['message']] ?? '';
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Blog Management</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/panel.css">
    <style>
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        .admin-header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            margin: -20px -20px 20px -20px;
            border-radius: 8px 8px 0 0;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 5px;
        }

        .btn-danger {
            background: #e74c3c;
        }

        .btn-success {
            background: #27ae60;
        }

        .btn:hover {
            opacity: 0.9;
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
                <h1>📝 Blog Management Panel</h1>
                <p>Manage your blog posts and content</p>
            </div>

            <?= $message ?>

            <div style="margin-bottom: 20px;">
                <a href="./create.php" class="btn btn-success">➕ Create New Post</a>
                <a href="./index.php" class="btn">👁️ View Website</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Views</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($posts)): ?>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td><?= $post['id'] ?></td>
                                <td><strong><?= htmlspecialchars($post['title']) ?></strong></td>
                                <td>
                                    <span style="background: #f38b8b; color: white; padding: 3px 8px; border-radius: 12px; font-size: 12px;">
                                        <?= ucfirst($post['category']) ?>
                                    </span>
                                </td>
                                <td>👁️ <?= $post['views'] ?></td>
                                <td><?= date('Y-m-d', strtotime($post['created_at'])) ?></td>
                                <td>
                                    <a href="./edit.php?id=<?= $post['id'] ?>" class="btn" style="padding: 5px 10px; font-size: 12px;">✏️ Edit</a>
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="delete_id" value="<?= $post['id'] ?>">
                                        <button type="submit" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;"
                                            onclick="return confirm('Are you sure you want to delete this post?')">🗑️ Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 20px;">
                                📝 No posts found. <a href="./create.php">Create your first post</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>" class="btn">← Previous</a>
                <?php endif; ?>
                <span>Page <?= $page ?></span>
                <?php if (count($posts) === $perPage): ?>
                    <a href="?page=<?= $page + 1 ?>" class="btn">Next →</a>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>

</html>