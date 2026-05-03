<!DOCTYPE html>
<html lang="en" dir="ltr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>

<div class="admin-wrapper">

    <!-- Overlay for mobile sidebar -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <span class="brand-icon">📝</span>
                <span class="brand-name" data-i18n="adminPanel">Admin Panel</span>
            </div>
            <button class="sidebar-close" id="sidebarClose" aria-label="Close sidebar">&#10005;</button>
        </div>

        <nav class="sidebar-nav">
            <ul>
                <li class="nav-item <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
                    <a href="panel.php" class="nav-link">
                        <span class="nav-icon">&#x1F4CA;</span>
                        <span class="nav-label" data-i18n="dashboard">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item <?= ($currentPage ?? '') === 'create' ? 'active' : '' ?>">
                    <a href="create.php" class="nav-link">
                        <span class="nav-icon">&#x2795;</span>
                        <span class="nav-label" data-i18n="createPost">Create Post</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php" target="_blank" class="nav-link">
                        <span class="nav-icon">&#x1F310;</span>
                        <span class="nav-label" data-i18n="viewSite">View Site</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link nav-logout">
                        <span class="nav-icon">&#x1F6AA;</span>
                        <span class="nav-label" data-i18n="logout">Logout</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="admin-profile">
                <div class="admin-avatar">&#x1F464;</div>
                <div class="admin-info">
                    <div class="admin-name"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></div>
                    <div class="admin-role" data-i18n="administrator">Administrator</div>
                </div>
            </div>
        </div>
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
                <h1 class="page-title" data-i18n="<?= htmlspecialchars($currentPage ?? '') ?>Title"><?= htmlspecialchars($pageTitle ?? '') ?></h1>
            </div>
            <div class="topbar-right">
                <button class="theme-toggle" id="themeToggle" title="Toggle dark/light mode">
                    <span id="themeIcon">🌙</span>
                </button>
                <div class="lang-switcher">
                    <button class="lang-btn" id="btnEn" onclick="setLang('en')">EN</button>
                    <button class="lang-btn" id="btnFa" onclick="setLang('fa')">FA</button>
                </div>
            </div>
        </header>

        <!-- CONTENT AREA -->
        <div class="content-area">
