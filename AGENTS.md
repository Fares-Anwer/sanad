# AGENTS.md — Sanad (سَنَد)

## Project status
No code yet. The sole source of truth is `PRD.md`. `idea.md` is an earlier Arabic draft — less precise, use PRD instead.

## Stack (pure, no frameworks)
- **PHP** — no Laravel/Symfony. Procedural + OOP. PDO prepared statements for all queries.
- **JavaScript** — vanilla ES6+. No React/Vue/jQuery.
- **CSS** — no Bootstrap/Tailwind. Custom variables, Flexbox, Grid, glassmorphism.
- **DB** — MySQL via XAMPP/WAMP, auto-setup on first run (`CREATE DATABASE IF NOT EXISTS`, `CREATE TABLE IF NOT EXISTS` in `includes/db.php`).

## File layout (from PRD)
```
/sanad/
├── index.php, login.php, register.php, logout.php
├── marketplace.php, device.php, add-device.php
├── request.php, dashboard-donor.php, dashboard-beneficiary.php
├── admin/  (index, listings, requests, users, action)
├── includes/  (db, auth, functions, config)
├── assets/  (css/style.js, js/{main,maps,validation}.js, images/)
└── uploads/  (devices/ public, medical-reports/ PROTECTED via .htaccess)
```

## Architecture must-haves
- **Roles**: `beneficiary`, `donor`, `admin`. Admin assigned manually (no self-register).
- **Failsafe DB**: `includes/db.php` connects with no DB selected first, creates DB + all tables via `IF NOT EXISTS` on every request.
- **Session auth**: PHP sessions. Role check at top of every protected page. Redirect unauthorized to login with error.
- **CSRF tokens** on all POST forms.
- **File upload**: UUID v4 rename, `finfo_file()` MIME check, whitelist `.jpg/.jpeg/.png/.webp/.pdf`.
- **Medical reports**: Never directly accessible via URL. Serve through authenticated PHP endpoint. `.htaccess` deny in `/uploads/medical-reports/`.
- **Request locking**: One active request per device at a time. On submit → status `under_request_review`. On reject → back to `active`.
- **Communication**: WhatsApp `https://wa.me/967...?text=...` + `tel:` links. No internal chat.
- **API key**: Google Maps JS API (pick location on add-device, display on device detail). Prepare static fallback for defense.

## Design tokens (from PRD)
- Font: `Tajawal` (Google Font, Arabic + Latin)
- Primary: `#00B4D8`, Dark: `#0077A8`, Background: `#F0F8FF`, Text: `#1A1A2E`
- RTL layout: `dir="rtl" lang="ar"` on all pages
- Breakpoints: 1200px (3-col), 768px (2-col), 480px (1-col)
- Touch targets: minimum 44×44px

## DB tables (4)
`users`, `devices`, `device_photos`, `requests` — see PRD §9.2 for exact SQL.
Device status machine: `pending_review → active → under_request_review → loaned` (or `rejected` anywhere).

## Yemen-specific data
21 governorates listed in PRD §17.1. Cascading dropdowns (governorate → district) for registration and filters.

## Commands
None yet — no package.json, no build tooling. `XAMPP/WAMP` local server expected.
