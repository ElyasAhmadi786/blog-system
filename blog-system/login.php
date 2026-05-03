<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If already logged in, redirect to panel
if (!empty($_SESSION['admin_logged_in'])) {
    header("Location: panel.php");
    exit;
}

require_once "./autoload.php";

use classes\Database;

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password =      $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        $conn = Database::getConnection();
        if (!$conn) {
            $error = 'Database connection failed. Please check your configuration.';
        } else {
            $stmt = $conn->prepare("SELECT id, first_name, last_name, password FROM users WHERE email = ? LIMIT 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows === 1) {
                $user = $result->fetch_assoc();

                // Verify against bcrypt hash only
                if (password_verify($password, $user['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id']        = $user['id'];
                    $_SESSION['admin_name']      = $user['first_name'] . ' ' . $user['last_name'];
                    header("Location: panel.php");
                    exit;
                }
            }

            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .login-form .form-group { margin-bottom: 18px; }
        .login-form .form-control { padding: 12px 16px; font-size: 15px; }
        .login-btn {
            width: 100%;
            padding: 13px;
            font-size: 15px;
            font-weight: 600;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: background 0.3s;
        }
        .login-btn:hover { background: var(--accent-h); }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 16px;
            font-size: 13px;
            color: var(--text-muted);
        }
        .back-link a { color: var(--accent); }
    </style>
</head>
<body>

<div class="login-page">
    <div class="login-box">
        <div class="login-logo">
            <span class="logo-icon">🔐</span>
            <h1 data-i18n="adminPanel">Admin Panel</h1>
            <p data-i18n="loginSubtitle">Sign in to manage your blog</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="login-form" autocomplete="off">
            <div class="form-group">
                <label for="email" data-i18n="emailLabel">Email Address</label>
                <input type="email" id="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="admin@gmail.com" required autofocus>
            </div>
            <div class="form-group">
                <label for="password" data-i18n="passwordLabel">Password</label>
                <input type="password" id="password" name="password" class="form-control"
                       placeholder="••••••••" required>
            </div>
            <button type="submit" class="login-btn" data-i18n="loginBtn">Sign In</button>
        </form>

        <a href="index.php" class="back-link">← <span data-i18n="backToSite">Back to website</span></a>
    </div>
</div>

<script>
const loginI18n = {
    en: {
        adminPanel:    'Admin Panel',
        loginSubtitle: 'Sign in to manage your blog',
        emailLabel:    'Email Address',
        passwordLabel: 'Password',
        loginBtn:      'Sign In',
        backToSite:    'Back to website',
    },
    fa: {
        adminPanel:    'پنل مدیریت',
        loginSubtitle: 'برای مدیریت وبلاگ وارد شوید',
        emailLabel:    'آدرس ایمیل',
        passwordLabel: 'رمز عبور',
        loginBtn:      'ورود',
        backToSite:    'بازگشت به سایت',
    }
};

function applyLoginLang(lang) {
    const t = loginI18n[lang] || loginI18n.en;
    document.documentElement.lang = lang === 'fa' ? 'fa' : 'en';
    document.documentElement.dir  = lang === 'fa' ? 'rtl' : 'ltr';
    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        if (t[key] !== undefined) el.textContent = t[key];
    });
}

(function () {
    const savedTheme = localStorage.getItem('admin_theme') || 'light';
    const savedLang  = localStorage.getItem('admin_lang')  || 'en';
    document.documentElement.setAttribute('data-theme', savedTheme);
    applyLoginLang(savedLang);
})();
</script>
</body>
</html>
