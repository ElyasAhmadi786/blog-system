<!DOCTYPE html>
<html lang="en" dir="ltr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>

<?php
// Load site settings (logo) and current user avatar for the layout.
// We only instantiate these once per request; the outer page may already have them.
if (!isset($_siteSettings)) {
    require_once __DIR__ . '/../autoload.php';
    use classes\Repository\SettingRepository;
    use classes\StorageTypes\MySQL;
    $_siteSettings = (new SettingRepository(new MySQL()))->getSettings();
}
$_adminAvatar = $_SESSION['admin_avatar'] ?? null;
$_adminName   = $_SESSION['admin_name']   ?? 'Admin';
$_avatarInitial = strtoupper(mb_substr(strip_tags($_adminName), 0, 1) ?: 'A');
?>

<div class="admin-wrapper">

    <!-- Overlay for mobile sidebar -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="panel.php" class="sidebar-brand">
                <?php if (!empty($_siteSettings['logo'])): ?>
                    <img src="assets/images/<?= htmlspecialchars(basename($_siteSettings['logo'])) ?>"
                         alt="Logo" class="brand-logo"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='inline'">
                    <span class="brand-icon" style="display:none">📝</span>
                <?php else: ?>
                    <span class="brand-icon">📝</span>
                <?php endif; ?>
                <span class="brand-name" data-i18n="adminPanel">Admin Panel</span>
            </a>
            <button class="sidebar-close" id="sidebarClose" aria-label="Close sidebar">&#10005;</button>
        </div>

        <nav class="sidebar-nav">
            <ul>

                <!-- Dashboard -->
                <li class="nav-item <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
                    <a href="panel.php" class="nav-link">
                        <span class="nav-icon">📊</span>
                        <span class="nav-label" data-i18n="dashboard">Dashboard</span>
                    </a>
                </li>

                <!-- ── Content section ── -->
                <li class="nav-group-label" data-i18n="navContent">Content</li>

                <li class="nav-item nav-dropdown">
                    <button class="nav-link nav-dropdown-toggle" data-dropdown="content" type="button">
                        <span class="nav-icon">📝</span>
                        <span class="nav-label" data-i18n="posts">Posts</span>
                        <span class="nav-arrow">›</span>
                    </button>
                    <ul class="nav-dropdown-menu">
                        <li class="<?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
                            <a href="panel.php" class="nav-link nav-sub-link" data-i18n="allPosts">All Posts</a>
                        </li>
                        <li class="<?= ($currentPage ?? '') === 'create' ? 'active' : '' ?>">
                            <a href="create.php" class="nav-link nav-sub-link" data-i18n="createPost">Create Post</a>
                        </li>
                    </ul>
                </li>

                <!-- ── Settings section ── -->
                <li class="nav-group-label" data-i18n="navAdminSettings">Settings</li>

                <li class="nav-item nav-dropdown">
                    <button class="nav-link nav-dropdown-toggle" data-dropdown="settings" type="button">
                        <span class="nav-icon">⚙️</span>
                        <span class="nav-label" data-i18n="settingsMenu">Settings</span>
                        <span class="nav-arrow">›</span>
                    </button>
                    <ul class="nav-dropdown-menu">
                        <li class="<?= ($currentPage ?? '') === 'settings' ? 'active' : '' ?>">
                            <a href="settings.php" class="nav-link nav-sub-link" data-i18n="generalSettings">General</a>
                        </li>
                        <li class="<?= ($currentPage ?? '') === 'profile' ? 'active' : '' ?>">
                            <a href="profile.php" class="nav-link nav-sub-link" data-i18n="myProfile">Profile</a>
                        </li>
                    </ul>
                </li>

                <!-- View Site -->
                <li class="nav-item">
                    <a href="index.php" target="_blank" class="nav-link">
                        <span class="nav-icon">🌐</span>
                        <span class="nav-label" data-i18n="viewSite">View Site</span>
                    </a>
                </li>

                <!-- Logout -->
                <li class="nav-item">
                    <a href="logout.php" class="nav-link nav-logout">
                        <span class="nav-icon">🚪</span>
                        <span class="nav-label" data-i18n="logout">Logout</span>
                    </a>
                </li>

            </ul>
        </nav>
    </aside>

    <!-- ===== MAIN CONTENT ===== -->
    <div class="main-content" id="mainContent">

        <!-- TOP BAR -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h1 class="page-title"><?= htmlspecialchars($pageTitle ?? '') ?></h1>
            </div>
            <div class="topbar-right">
                <button class="theme-toggle" id="themeToggle" title="Toggle dark/light mode">
                    <span id="themeIcon">🌙</span>
                </button>
                <div class="lang-switcher">
                    <button class="lang-btn" id="btnEn" onclick="setLang('en')">EN</button>
                    <button class="lang-btn" id="btnFa" onclick="setLang('fa')">FA</button>
                </div>

                <!-- User / avatar menu -->
                <div class="user-menu" id="userMenu">
                    <button class="user-menu-btn" id="userMenuBtn" type="button" aria-label="User menu">
                        <?php if (!empty($_adminAvatar)): ?>
                            <img src="assets/images/avatars/<?= htmlspecialchars(basename($_adminAvatar)) ?>"
                                 alt="Avatar" class="topbar-avatar"
                                 onerror="this.style.display='none';document.getElementById('avatarFallback').style.display='flex'">
                            <span class="topbar-avatar-fallback" id="avatarFallback" style="display:none">
                                <?= htmlspecialchars($_avatarInitial) ?>
                            </span>
                        <?php else: ?>
                            <span class="topbar-avatar-fallback" id="avatarFallback">
                                <?= htmlspecialchars($_avatarInitial) ?>
                            </span>
                        <?php endif; ?>
                        <span class="user-menu-name"><?= htmlspecialchars($_adminName) ?></span>
                        <span class="user-menu-arrow">▾</span>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="profile.php" class="user-dropdown-item">
                            <span>👤</span> <span data-i18n="myProfile">My Profile</span>
                        </a>
                        <a href="settings.php" class="user-dropdown-item">
                            <span>⚙️</span> <span data-i18n="generalSettings">Settings</span>
                        </a>
                        <div class="user-dropdown-divider"></div>
                        <a href="logout.php" class="user-dropdown-item user-dropdown-logout">
                            <span>🚪</span> <span data-i18n="logout">Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- CONTENT AREA -->
        <div class="content-area">
