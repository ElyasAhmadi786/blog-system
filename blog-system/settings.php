<?php
require_once "./includes/auth.php";
require_once "./autoload.php";

use classes\Setting;
use classes\Repository\SettingRepository;
use classes\StorageTypes\MySQL;

$settingRepository = new SettingRepository(new MySQL());
$settings          = $settingRepository->getSettings();

$message = null;
$error   = null;

// ── POST: save settings ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $siteTitle   = trim($_POST['site_title']   ?? '');
    $keywords    = trim($_POST['keywords']     ?? '');
    $description = trim($_POST['description']  ?? '');
    $author      = trim($_POST['author']       ?? '');
    $footer      = trim($_POST['footer']       ?? '');

    if ($siteTitle === '') {
        $error = "Site title is required.";
    } else {

        // ── Logo upload ──────────────────────────────────────────
        $logoFilename = $settings['logo'] ?? null;   // keep old unless replaced

        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
            $ext        = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowedExt, true)) {
                $error = "Logo must be JPG, PNG, GIF, WebP, or SVG.";
            } else {
                $uploadDir = 'assets/images/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $newName = 'logo_' . bin2hex(random_bytes(12)) . '.' . $ext;
                $dest    = $uploadDir . $newName;

                if (move_uploaded_file($_FILES['logo']['tmp_name'], $dest)) {
                    // Remove old logo file if it was an uploaded one
                    if ($logoFilename && strpos($logoFilename, 'logo_') === 0
                        && file_exists($uploadDir . $logoFilename)) {
                        unlink($uploadDir . $logoFilename);
                    }
                    $logoFilename = $newName;
                } else {
                    $error = "Failed to upload logo. Check folder permissions.";
                }
            }
        }

        if (!$error) {
            $logoValue = $logoFilename; // store just the filename

            if ($settings) {
                // Update existing record
                $ok = $settingRepository->update((int)$settings['id'], [
                    'title'       => $siteTitle,
                    'keywords'    => $keywords,
                    'description' => $description,
                    'author'      => $author,
                    'logo'        => $logoValue,
                    'footer'      => $footer,
                ]);
            } else {
                // Insert first record
                $ok = $settingRepository->store([
                    'title'       => $siteTitle,
                    'keywords'    => $keywords,
                    'description' => $description,
                    'author'      => $author,
                    'logo'        => $logoValue,
                    'footer'      => $footer,
                ]);
            }

            if ($ok) {
                // Reload so the layout picks up new logo immediately
                header("Location: settings.php?saved=1");
                exit;
            } else {
                $error = "Failed to save settings. Please try again.";
            }
        }
    }
}

// ── Show success flash ─────────────────────────────────────────────
if (isset($_GET['saved'])) {
    $message = ['type' => 'success', 'text' => '✅ Settings saved successfully!'];
    // Reload settings after save
    $settings = $settingRepository->getSettings();
}

$pageTitle   = 'General Settings';
$currentPage = 'settings';
// Pre-load site settings for the layout (logo in header)
$_siteSettings = $settings;

include 'includes/admin_layout_start.php';
?>

<div class="card">
    <div class="card-header">
        <h2>⚙️ General Settings</h2>
    </div>
    <div class="card-body">

        <?php if ($message): ?>
            <div class="alert alert-<?= htmlspecialchars($message['type']) ?>">
                <?= $message['text'] ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">

            <!-- Site Logo -->
            <div class="form-group">
                <label>Site Logo</label>

                <?php if (!empty($settings['logo'])): ?>
                    <div class="image-preview-container" id="currentLogoWrap"
                         style="margin-bottom:10px;">
                        <img src="assets/images/<?= htmlspecialchars(basename($settings['logo'])) ?>"
                             alt="Current logo"
                             class="image-preview"
                             id="currentLogo"
                             style="max-height:80px;object-fit:contain;"
                             onerror="this.parentNode.style.display='none'">
                        <span style="font-size:12px;color:var(--text-muted);">Current logo</span>
                    </div>
                <?php endif; ?>

                <input type="file" name="logo" id="logoInput" class="form-control"
                       accept="image/jpeg,image/png,image/gif,image/webp,image/svg+xml"
                       onchange="previewLogo(this)">
                <div class="form-hint">JPG, PNG, GIF, WebP, SVG · Recommended: transparent PNG, max 400×120 px</div>

                <div class="image-preview-container" id="logoPreviewWrap" style="display:none;margin-top:10px;">
                    <img id="logoPreview" src="" alt="Logo preview"
                         class="image-preview" style="max-height:80px;object-fit:contain;">
                    <button type="button" class="btn btn-secondary btn-sm"
                            onclick="clearLogo()">✕ Remove new logo</button>
                </div>
            </div>

            <!-- Site Title -->
            <div class="form-group">
                <label for="site_title">Site Title *</label>
                <input type="text" id="site_title" name="site_title" class="form-control" required
                       value="<?= htmlspecialchars($_POST['site_title'] ?? $settings['title'] ?? '') ?>"
                       placeholder="e.g. My Blog">
            </div>

            <!-- Meta Keywords -->
            <div class="form-group">
                <label for="keywords">Meta Keywords</label>
                <input type="text" id="keywords" name="keywords" class="form-control"
                       value="<?= htmlspecialchars($_POST['keywords'] ?? $settings['keywords'] ?? '') ?>"
                       placeholder="news, sport, tech — comma separated">
            </div>

            <!-- Meta Description -->
            <div class="form-group">
                <label for="description">Default Meta Description</label>
                <textarea id="description" name="description" class="form-control" rows="3"
                          placeholder="A short description shown in search engine results…"><?= htmlspecialchars($_POST['description'] ?? $settings['description'] ?? '') ?></textarea>
            </div>

            <!-- Author -->
            <div class="form-group">
                <label for="author">Site Author</label>
                <input type="text" id="author" name="author" class="form-control"
                       value="<?= htmlspecialchars($_POST['author'] ?? $settings['author'] ?? '') ?>"
                       placeholder="Your name or organisation">
            </div>

            <!-- Footer Text -->
            <div class="form-group">
                <label for="footer">Footer Copyright Text</label>
                <input type="text" id="footer" name="footer" class="form-control"
                       value="<?= htmlspecialchars($_POST['footer'] ?? $settings['footer'] ?? '') ?>"
                       placeholder="© 2024 My Blog. All rights reserved.">
            </div>

            <div style="display:flex;gap:10px;margin-top:28px;flex-wrap:wrap;">
                <button type="submit" class="btn btn-success">💾 Save Settings</button>
                <a href="panel.php" class="btn btn-secondary">← Back to Dashboard</a>
            </div>
        </form>
    </div>
</div>

<script>
function previewLogo(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('logoPreview').src = e.target.result;
        document.getElementById('logoPreviewWrap').style.display = 'flex';
        const cw = document.getElementById('currentLogoWrap');
        if (cw) cw.style.opacity = '0.35';
    };
    reader.readAsDataURL(file);
}

function clearLogo() {
    document.getElementById('logoInput').value  = '';
    document.getElementById('logoPreview').src  = '';
    document.getElementById('logoPreviewWrap').style.display = 'none';
    const cw = document.getElementById('currentLogoWrap');
    if (cw) cw.style.opacity = '1';
}
</script>

<?php include 'includes/admin_layout_end.php'; ?>
