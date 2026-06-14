# AGENTS.md — Sanad (سَنَد)

## Project status
Zero code written. Only planning docs exist. Build order = phase order in `milestone.md`.

## Source of truth hierarchy
1. `PRD.md` — product requirements, exact SQL (§9.2), design tokens (§11.2), 21 governorates (§17.1), 4 medical categories (§17.2)
2. `milestone.md` — phase dependency graph, deliverables, risk notes
3. `tasks.md` — actionable checklist broken into granular items; mark `[x]` as completed

`idea.md` is an earlier Arabic draft — ignore it.

## Stack
- **PHP** — no Laravel/Symfony. Procedural + OOP. PDO prepared statements for all queries.
- **JavaScript** — vanilla ES6+. No React/Vue/jQuery.
- **CSS** — no Bootstrap/Tailwind. Custom variables, Flexbox, Grid, glassmorphism.
- **DB** — MySQL via XAMPP/WAMP, auto-setup on first run (`CREATE DATABASE IF NOT EXISTS`, `CREATE TABLE IF NOT EXISTS` in `includes/db.php`).

## File layout
```
/sanad/
├── index.php, login.php, register.php, logout.php
├── marketplace.php, device.php, add-device.php
├── request.php, dashboard-donor.php, dashboard-beneficiary.php
├── admin/  (index, listings, requests, users, action)
├── includes/  (db, auth, functions, config)
├── assets/  (css/style.css, js/{main,maps,validation}.js, images/)
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
- **API key**: Google Maps JS API (pick location, display on detail). Static fallback image at `assets/images/map-fallback.png`.

## Design tokens
- Font: `Tajawal` (Google Font, Arabic + Latin)
- Primary: `#00B4D8`, Dark: `#0077A8`, Background: `#F0F8FF`, Text: `#1A1A2E`
- RTL: `dir="rtl" lang="ar"` on all pages
- Breakpoints: 1200px (3-col), 768px (2-col), 480px (1-col)
- Touch targets: minimum 44×44px

## DB tables (4)
`users`, `devices`, `device_photos`, `requests` — exact SQL in PRD §9.2.
Device status machine: `pending_review → active → under_request_review → loaned` (or `rejected` anywhere).
Request status machine: `pending → approved` (or `rejected`). Rejected request → device back to `active`.

## Yemen data
- 21 governorates in PRD §17.1
- 4 medical categories: `respiratory`, `mobility`, `beds_clinical`, `diagnostic`
- Phone format: Yemeni (+967), strip non-digits, `ltrim($phone, '0')`, prepend `967`
- Cascading dropdowns (governorate → district) required on registration and marketplace filters

## Build priority
Phases must follow dependency graph from `milestone.md`:
```
Phase 0 → Phase 1 → Phase 2 → Phase 4 → Phase 5 → Phase 6 → Phase 7 → Phase 8
                      ↓
                    Phase 3
```
Phase 3 can run in parallel with Phase 2. Within a phase, items are unordered unless stated.

## Commands
None. No package.json, no build tooling, no test runner. XAMPP/WAMP local server with Apache + MySQL expected. To verify: open browser to `http://localhost/sanad/`.
