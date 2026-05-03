        </div><!-- /.content-area -->
    </div><!-- /.main-content -->
</div><!-- /.admin-wrapper -->

<script>
// ─── Translations ────────────────────────────────────────────────
const i18n = {
    en: {
        adminPanel:     'Admin Panel',
        dashboard:      'Dashboard',
        dashboardTitle: 'Dashboard',
        createPost:     'Create Post',
        createTitle:    'Create New Post',
        editTitle:      'Edit Post',
        viewSite:       'View Site',
        logout:         'Logout',
        administrator:  'Administrator',
        // Table
        id:             'ID',
        title:          'Title',
        category:       'Category',
        views:          'Views',
        date:           'Date',
        actions:        'Actions',
        edit:           'Edit',
        delete:         'Delete',
        // Pagination
        previous:       '← Previous',
        next:           'Next →',
        page:           'Page',
        // Form
        postTitle:      'Post Title *',
        postCategory:   'Category *',
        postContent:    'Content *',
        featuredImage:  'Featured Image',
        createBtn:      '📝 Create Post',
        updateBtn:      '💾 Update Post',
        backBtn:        '← Back to Dashboard',
        viewPostBtn:    '👁 View Post',
        // Messages
        confirmDelete:  'Are you sure you want to delete this post?',
        noPosts:        'No posts found.',
        createFirst:    'Create your first post',
        createNewPost:  '➕ Create New Post',
        viewWebsite:    '👁 View Website',
        // Categories
        political:      'Political',
        sport:          'Sport',
        social:         'Social',
    },
    fa: {
        adminPanel:     'پنل مدیریت',
        dashboard:      'داشبورد',
        dashboardTitle: 'داشبورد',
        createPost:     'ایجاد پست',
        createTitle:    'ایجاد پست جدید',
        editTitle:      'ویرایش پست',
        viewSite:       'مشاهده سایت',
        logout:         'خروج',
        administrator:  'مدیر',
        // Table
        id:             'شناسه',
        title:          'عنوان',
        category:       'دسته‌بندی',
        views:          'بازدید',
        date:           'تاریخ',
        actions:        'عملیات',
        edit:           'ویرایش',
        delete:         'حذف',
        // Pagination
        previous:       '→ قبلی',
        next:           'بعدی ←',
        page:           'صفحه',
        // Form
        postTitle:      'عنوان پست *',
        postCategory:   'دسته‌بندی *',
        postContent:    'محتوا *',
        featuredImage:  'تصویر شاخص',
        createBtn:      '📝 ایجاد پست',
        updateBtn:      '💾 بروزرسانی پست',
        backBtn:        'بازگشت به داشبورد →',
        viewPostBtn:    'مشاهده پست 👁',
        // Messages
        confirmDelete:  'آیا مطمئنید که می‌خواهید این پست را حذف کنید؟',
        noPosts:        'هیچ پستی یافت نشد.',
        createFirst:    'اولین پست خود را ایجاد کنید',
        createNewPost:  '➕ ایجاد پست جدید',
        viewWebsite:    '👁 مشاهده وبسایت',
        // Categories
        political:      'سیاسی',
        sport:          'ورزشی',
        social:         'اجتماعی',
    }
};

// ─── Language ────────────────────────────────────────────────────
function setLang(lang) {
    localStorage.setItem('admin_lang', lang);
    applyLang(lang);
}

function applyLang(lang) {
    const t = i18n[lang] || i18n.en;
    const isRtl = lang === 'fa';

    // Direction & lang attribute
    document.documentElement.lang = lang === 'fa' ? 'fa' : 'en';
    document.documentElement.dir  = isRtl ? 'rtl' : 'ltr';

    // Active button
    document.getElementById('btnEn').classList.toggle('active', lang === 'en');
    document.getElementById('btnFa').classList.toggle('active', lang === 'fa');

    // Translate all elements with data-i18n
    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        if (t[key] !== undefined) el.textContent = t[key];
    });

    // Page title heading (uses a special key like "dashboardTitle")
    const titleEl = document.querySelector('.page-title');
    if (titleEl) {
        const pageKey = titleEl.getAttribute('data-i18n');
        if (pageKey && t[pageKey] !== undefined) titleEl.textContent = t[pageKey];
    }
}

// ─── Dark / Light Mode ────────────────────────────────────────────
function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    document.getElementById('themeIcon').textContent = theme === 'dark' ? '☀️' : '🌙';
    localStorage.setItem('admin_theme', theme);
}

document.getElementById('themeToggle').addEventListener('click', () => {
    const current = document.documentElement.getAttribute('data-theme') || 'light';
    applyTheme(current === 'dark' ? 'light' : 'dark');
});

// ─── Sidebar Drawer ───────────────────────────────────────────────
const sidebar        = document.getElementById('sidebar');
const mainContent    = document.getElementById('mainContent');
const overlay        = document.getElementById('sidebarOverlay');
const menuToggle     = document.getElementById('menuToggle');
const sidebarClose   = document.getElementById('sidebarClose');

function openSidebar() {
    sidebar.classList.add('open');
    overlay.classList.add('visible');
}
function closeSidebar() {
    sidebar.classList.remove('open');
    overlay.classList.remove('visible');
}
function toggleSidebar() {
    if (sidebar.classList.contains('open')) {
        closeSidebar();
    } else {
        openSidebar();
    }
}

menuToggle.addEventListener('click', toggleSidebar);
sidebarClose.addEventListener('click', closeSidebar);
overlay.addEventListener('click', closeSidebar);

// On desktop: sidebar always visible; on mobile: drawer
function handleResize() {
    if (window.innerWidth >= 992) {
        sidebar.classList.add('open');
        overlay.classList.remove('visible');
    } else {
        sidebar.classList.remove('open');
    }
}
window.addEventListener('resize', handleResize);

// ─── Init on page load ────────────────────────────────────────────
(function init() {
    const savedTheme = localStorage.getItem('admin_theme') || 'light';
    const savedLang  = localStorage.getItem('admin_lang')  || 'en';
    applyTheme(savedTheme);
    applyLang(savedLang);
    handleResize();
})();
</script>
</body>
</html>
