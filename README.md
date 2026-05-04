# Blog System — Complete Admin Panel

A PHP/MySQL blog system with a fully-featured admin panel.

---

## Features

| Feature | Detail |
|---|---|
| **Dashboard** | Stats cards (total posts, published, drafts, views, users) + posts table with thumbnails |
| **Post management** | Create / Edit / Delete · Status (draft / published) · Quill rich-text editor · Image upload with live preview · Search & filter |
| **General Settings** | Logo upload · Site title · Meta keywords & description · Author · Footer text |
| **Profile** | Full name · Email · Password (bcrypt) · Avatar upload — avatar shown in topbar |
| **Sidebar** | Collapsible dropdowns (JS click, localStorage state) · Logo in header |
| **Dark mode** | Full light / dark theme toggle |
| **RTL** | English / Farsi switcher |
| **Responsive** | Works on desktop and mobile |

---

## Quick Start (fresh install)

1. Import `blog-system/database.sql` into MySQL.
2. Copy the `blog-system/` folder to your web server root (or a sub-folder).
3. Edit `classes/Database.php` with your DB host / user / password.
4. Open `http://localhost/blog-system/login.php`.
   - Default: `admin@gmail.com` / `admin123`
5. Go to **Settings** to upload your logo and set the site title.

---

## Upgrading an existing installation

If you already have `blog_db` set up, run the migration script once:

```sql
-- In your MySQL client:
source /path/to/blog-system/migrate.sql
```

This adds:
- `status ENUM('draft','published')` to the `posts` table (all existing posts default to `published`).
- `avatar VARCHAR(255)` to the `users` table.

No existing data is changed.

---

## File Structure (admin panel)

```
blog-system/
├── panel.php          ← Dashboard (stats + post list)
├── create.php         ← Create post (Quill, image upload, status)
├── edit.php           ← Edit post
├── settings.php       ← General settings (logo, title, footer…)
├── profile.php        ← Admin profile (name, email, password, avatar)
├── login.php / logout.php
├── includes/
│   ├── admin_layout_start.php   ← Shared header + collapsible sidebar
│   └── admin_layout_end.php     ← Shared footer + JS (dropdowns, theme, i18n)
├── assets/
│   ├── css/admin.css            ← All admin styles
│   └── images/                  ← Post images, logo
│       └── avatars/             ← Admin avatar uploads
├── classes/
│   ├── Database.php
│   ├── Post.php / Setting.php
│   └── Repository/  StorageTypes/
├── database.sql       ← Full schema + seed data
└── migrate.sql        ← Incremental migration for existing installs
```

---

## Image Serving

All images are uploaded to `assets/images/` (post thumbnails, logo) or
`assets/images/avatars/` (admin avatar) and served as standard `<img>` tags
with correct relative `src` paths — no broken images.

