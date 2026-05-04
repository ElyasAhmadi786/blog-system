        </div><!-- /.content-area -->
    </div><!-- /.main-content -->
</div><!-- /.admin-wrapper -->

<script>
// ─── Translations ────────────────────────────────────────────────
const i18n = {
    en: {
        adminPanel:       'Admin Panel',
        dashboard:        'Dashboard',
        dashboardTitle:   'Dashboard',
        navContent:       'Content',
        navAdminSettings: 'Settings',
        posts:            'Posts',
        allPosts:         'All Posts',
        createPost:       'Create Post',
        createTitle:      'Create New Post',
        editTitle:        'Edit Post',
        settingsMenu:     'Settings',
        generalSettings:  'General Settings',
        myProfile:        'My Profile',
        viewSite:         'View Site',
        logout:           'Logout',
        administrator:    'Administrator',
        // Table
        id:               'ID',
        thumbnail:        'Image',
        title:            'Title',
        category:         'Category',
        status:           'Status',
        views:            'Views',
        date:             'Date',
        actions:          'Actions',
        edit:             'Edit',
        delete:           'Delete',
        // Pagination
        previous:         '← Previous',
        next:             'Next →',
        page:             'Page',
        // Form
        postTitle:        'Post Title *',
        postCategory:     'Category *',
        postStatus:       'Status',
        postContent:      'Content *',
        featuredImage:    'Featured Image',
        createBtn:        '📝 Create Post',
        updateBtn:        '💾 Update Post',
        backBtn:          '← Back to Dashboard',
        viewPostBtn:      '👁 View Post',
        // Messages
        confirmDelete:    'Are you sure you want to delete this post?',
        noPosts:          'No posts found.',
        createFirst:      'Create your first post',
        createNewPost:    '➕ Create New Post',
        viewWebsite:      '👁 View Website',
        // Categories
        political:        'Political',
        sport:            'Sport',
        social:           'Social',
        // Status
        published:        'Published',
        draft:            'Draft',
    },
    fa: {
        adminPanel:       'پنل مدیریت',
        dashboard:        'داشبورد',
        dashboardTitle:   'داشبورد',
        navContent:       'محتوا',
        navAdminSettings: 'تنظیمات',
        posts:            'پست‌ها',
        allPosts:         'همه پست‌ها',
        createPost:       'ایجاد پست',
        createTitle:      'ایجاد پست جدید',
        editTitle:        'ویرایش پست',
        settingsMenu:     'تنظیمات',
        generalSettings:  'تنظیمات کلی',
        myProfile:        'پروفایل من',
        viewSite:         'مشاهده سایت',
        logout:           'خروج',
        administrator:    'مدیر',
        id:               'شناسه',
        thumbnail:        'تصویر',
        title:            'عنوان',
        category:         'دسته‌بندی',
        status:           'وضعیت',
        views:            'بازدید',
        date:             'تاریخ',
        actions:          'عملیات',
        edit:             'ویرایش',
        delete:           'حذف',
        previous:         '→ قبلی',
        next:             'بعدی ←',
        page:             'صفحه',
        postTitle:        'عنوان پست *',
        postCategory:     'دسته‌بندی *',
        postStatus:       'وضعیت',
        postContent:      'محتوا *',
        featuredImage:    'تصویر شاخص',
        createBtn:        '📝 ایجاد پست',
        updateBtn:        '💾 بروزرسانی پست',
        backBtn:          'بازگشت به داشبورد →',
        viewPostBtn:      'مشاهده پست 👁',
        confirmDelete:    'آیا مطمئنید که می‌خواهید این پست را حذف کنید؟',
        noPosts:          'هیچ پستی یافت نشد.',
        createFirst:      'اولین پست خود را ایجاد کنید',
        createNewPost:    '➕ ایجاد پست جدید',
        viewWebsite:      '👁 مشاهده وبسایت',
        political:        'سیاسی',
        sport:            'ورزشی',
        social:           'اجتماعی',
        published:        'منتشر شده',
        draft:            'پیش‌نویس',
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

    document.documentElement.lang = lang === 'fa' ? 'fa' : 'en';
    document.documentElement.dir  = isRtl ? 'rtl' : 'ltr';

    document.getElementById('btnEn').classList.toggle('active', lang === 'en');
    document.getElementById('btnFa').classList.toggle('active', lang === 'fa');

    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        if (t[key] !== undefined) el.textContent = t[key];
    });
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

// ─── Sidebar Drawer (mobile) ──────────────────────────────────────
const sidebar      = document.getElementById('sidebar');
const overlay      = document.getElementById('sidebarOverlay');
const menuToggle   = document.getElementById('menuToggle');
const sidebarClose = document.getElementById('sidebarClose');

function openSidebar()  { sidebar.classList.add('open');    overlay.classList.add('visible'); }
function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('visible'); }

menuToggle.addEventListener('click', () =>
    sidebar.classList.contains('open') ? closeSidebar() : openSidebar()
);
sidebarClose.addEventListener('click', closeSidebar);
overlay.addEventListener('click', closeSidebar);

function handleResize() {
    if (window.innerWidth >= 992) {
        sidebar.classList.add('open');
        overlay.classList.remove('visible');
    } else {
        sidebar.classList.remove('open');
    }
}
window.addEventListener('resize', handleResize);

// ─── Sidebar Collapsible Dropdowns ───────────────────────────────
// Map page names to their parent dropdown key
const PAGE_DROPDOWN_MAP = {
    dashboard: 'content',
    create:    'content',
    edit:      'content',
    settings:  'settings',
    profile:   'settings',
};

// PHP injects the current page name so JS can auto-expand the right section
const CURRENT_PAGE = <?= json_encode($currentPage ?? '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

document.querySelectorAll('.nav-dropdown-toggle').forEach(function (btn) {
    const ddKey    = btn.dataset.dropdown;
    const dropdown = btn.closest('.nav-dropdown');
    const storageKey = 'sidebar_dd_' + ddKey;

    // Determine initial state: auto-expand if current page belongs here,
    // otherwise restore whatever the user last chose.
    const belongsHere  = PAGE_DROPDOWN_MAP[CURRENT_PAGE] === ddKey;
    const savedState   = localStorage.getItem(storageKey);
    const shouldExpand = belongsHere || savedState === 'true';

    if (shouldExpand) {
        dropdown.classList.add('expanded');
        localStorage.setItem(storageKey, 'true');
    }

    // Click handler: toggle and persist
    btn.addEventListener('click', function () {
        const expanded = !dropdown.classList.contains('expanded');
        dropdown.classList.toggle('expanded', expanded);
        localStorage.setItem(storageKey, expanded ? 'true' : 'false');
    });
});

// ─── User / Avatar Dropdown ───────────────────────────────────────
(function () {
    const userMenuBtn  = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');
    if (!userMenuBtn || !userDropdown) return;

    userMenuBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        userDropdown.classList.toggle('open');
    });

    document.addEventListener('click', function () {
        userDropdown.classList.remove('open');
    });

    // Prevent click inside dropdown from closing it
    userDropdown.addEventListener('click', function (e) {
        e.stopPropagation();
    });
})();

// ─── Delete confirmation helper ───────────────────────────────────
function getDeleteConfirm() {
    const lang = localStorage.getItem('admin_lang') || 'en';
    const t = i18n[lang] || i18n.en;
    return t.confirmDelete || 'Are you sure you want to delete this post?';
}

// ─── Init ─────────────────────────────────────────────────────────
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
