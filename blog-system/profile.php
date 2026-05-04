<?php
require_once "./includes/auth.php";
require_once "./autoload.php";

use classes\Database;

$conn    = Database::getConnection();
$adminId = (int)$_SESSION['admin_id'];

// ── Fetch current user data ────────────────────────────────────────
$stmt = $conn->prepare("SELECT id, first_name, last_name, email, avatar FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();

if (!$userData) {
    // Should never happen for a logged-in user; treat as fatal
    session_destroy();
    header("Location: login.php");
    exit;
}

$message = null;
$errors  = [];

// ── POST: update profile ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstName = trim($_POST['first_name'] ?? '');
    $lastName  = trim($_POST['last_name']  ?? '');
    $email     = trim($_POST['email']      ?? '');
    $password  =      $_POST['password']   ?? '';
    $passConf  =      $_POST['password_confirm'] ?? '';

    // Validation
    if ($firstName === '')                   $errors[] = "First name is required.";
    if ($lastName  === '')                   $errors[] = "Last name is required.";
    if ($email     === '')                   $errors[] = "Email address is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Please enter a valid email address.";

    // Password: only change if the field is filled
    $newHashedPassword = null;
    if ($password !== '') {
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters.";
        } elseif ($password !== $passConf) {
            $errors[] = "Passwords do not match.";
        } else {
            $newHashedPassword = password_hash($password, PASSWORD_DEFAULT);
        }
    }

    // Avatar upload
    $avatarFilename = $userData['avatar'];  // keep old unless replaced

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext        = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt, true)) {
            $errors[] = "Avatar must be JPG, PNG, GIF, or WebP.";
        } else {
            $avatarDir = 'assets/images/avatars/';
            if (!is_dir($avatarDir)) {
                mkdir($avatarDir, 0755, true);
            }

            $newName = 'avatar_' . bin2hex(random_bytes(12)) . '.' . $ext;
            $dest    = $avatarDir . $newName;

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
                // Remove old avatar file
                if ($avatarFilename && file_exists($avatarDir . basename($avatarFilename))) {
                    unlink($avatarDir . basename($avatarFilename));
                }
                $avatarFilename = $newName;   // store just the filename
            } else {
                $errors[] = "Failed to upload avatar. Check folder permissions.";
            }
        }
    }

    if (empty($errors)) {
        // Check email uniqueness (other than own record)
        $stmtChk = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
        $stmtChk->bind_param("si", $email, $adminId);
        $stmtChk->execute();
        if ($stmtChk->get_result()->num_rows > 0) {
            $errors[] = "This email address is already in use by another account.";
        }
    }

    if (empty($errors)) {
        // Build dynamic UPDATE query
        $fields = "first_name=?, last_name=?, email=?, avatar=?";
        $params = [$firstName, $lastName, $email, $avatarFilename];
        $types  = "ssss";

        if ($newHashedPassword) {
            $fields  .= ", password=?";
            $params[] = $newHashedPassword;
            $types   .= "s";
        }

        $params[] = $adminId;
        $types   .= "i";

        $stmtUpd = $conn->prepare("UPDATE users SET $fields WHERE id=?");
        $stmtUpd->bind_param($types, ...$params);

        if ($stmtUpd->execute()) {
            // Refresh session so topbar avatar updates immediately
            $_SESSION['admin_name']   = $firstName . ' ' . $lastName;
            $_SESSION['admin_avatar'] = $avatarFilename;

            header("Location: profile.php?saved=1");
            exit;
        } else {
            $errors[] = "Database error – could not save profile.";
        }
    }
}

// ── Success flash ──────────────────────────────────────────────────
if (isset($_GET['saved'])) {
    $message = ['type' => 'success', 'text' => '✅ Profile updated successfully!'];
    // Reload user data with a fresh query so we see the just-saved values
    $stmtRefresh = $conn->prepare(
        "SELECT id, first_name, last_name, email, avatar FROM users WHERE id = ? LIMIT 1"
    );
    $stmtRefresh->bind_param("i", $adminId);
    $stmtRefresh->execute();
    $userData = $stmtRefresh->get_result()->fetch_assoc();
}

$pageTitle   = 'My Profile';
$currentPage = 'profile';

include 'includes/admin_layout_start.php';

// Avatar helpers
$avatarFilename = $userData['avatar'] ?? null;
$avatarInitial  = strtoupper(mb_substr(($userData['first_name'] ?? 'A'), 0, 1));
?>

<div class="card" style="max-width:700px;margin:0 auto;">
    <div class="card-header">
        <h2>👤 My Profile</h2>
    </div>
    <div class="card-body">

        <?php if ($message): ?>
            <div class="alert alert-<?= htmlspecialchars($message['type']) ?>">
                <?= $message['text'] ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $e): ?>
                    <div>❌ <?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">

            <!-- Avatar section -->
            <div class="form-group">
                <label>Profile Picture</label>
                <div class="avatar-preview-wrap">
                    <?php if (!empty($avatarFilename)): ?>
                        <img src="assets/images/avatars/<?= htmlspecialchars(basename($avatarFilename)) ?>"
                             alt="Avatar"
                             class="avatar-preview"
                             id="avatarImg"
                             onerror="this.style.display='none';document.getElementById('avatarInitial').style.display='flex'">
                        <div class="avatar-fallback" id="avatarInitial" style="display:none;">
                            <?= htmlspecialchars($avatarInitial) ?>
                        </div>
                    <?php else: ?>
                        <div class="avatar-fallback" id="avatarInitial">
                            <?= htmlspecialchars($avatarInitial) ?>
                        </div>
                    <?php endif; ?>

                    <div>
                        <input type="file" name="avatar" id="avatarInput" class="form-control"
                               accept="image/jpeg,image/png,image/gif,image/webp"
                               onchange="previewAvatar(this)">
                        <div class="form-hint">JPG, PNG, GIF, WebP · Shown in the top-right corner</div>
                    </div>
                </div>
            </div>

            <!-- Name row -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" required
                           value="<?= htmlspecialchars($_POST['first_name'] ?? $userData['first_name'] ?? '') ?>"
                           placeholder="First name">
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" required
                           value="<?= htmlspecialchars($_POST['last_name'] ?? $userData['last_name'] ?? '') ?>"
                           placeholder="Last name">
                </div>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" class="form-control" required
                       value="<?= htmlspecialchars($_POST['email'] ?? $userData['email'] ?? '') ?>"
                       placeholder="admin@example.com">
            </div>

            <!-- Password (only filled if changing) -->
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" class="form-control"
                       placeholder="Leave blank to keep current password" autocomplete="new-password">
                <div class="form-hint">Minimum 8 characters. Leave blank to keep your current password.</div>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirm New Password</label>
                <input type="password" id="password_confirm" name="password_confirm" class="form-control"
                       placeholder="Repeat new password" autocomplete="new-password">
            </div>

            <div style="display:flex;gap:10px;margin-top:28px;flex-wrap:wrap;">
                <button type="submit" class="btn btn-success">💾 Save Profile</button>
                <a href="panel.php" class="btn btn-secondary">← Back to Dashboard</a>
            </div>
        </form>
    </div>
</div>

<script>
function previewAvatar(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        let img = document.getElementById('avatarImg');
        if (!img) {
            // No img element yet – replace the fallback div
            const fallback = document.getElementById('avatarInitial');
            img = document.createElement('img');
            img.id        = 'avatarImg';
            img.alt       = 'Avatar';
            img.className = 'avatar-preview';
            fallback.parentNode.insertBefore(img, fallback);
            fallback.style.display = 'none';
        }
        img.src = e.target.result;
        img.style.display = '';
        const initial = document.getElementById('avatarInitial');
        if (initial) initial.style.display = 'none';
    };
    reader.readAsDataURL(file);
}
</script>

<?php include 'includes/admin_layout_end.php'; ?>
