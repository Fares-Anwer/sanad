# Tasks — Sanad Platform

**Project:** Sanad (سَنَد) — Medical Equipment Solidarity Platform  
**Stack:** Pure PHP + Vanilla JS + CSS3 + MySQL (XAMPP/WAMP)  
**Source:** milestone.md (8 Phases, 8 Weeks)

---

**LEGEND**

- `[ ]` — Pending
- `[x]` — Done

---

## Phase 0 — Setup & Architecture (Week 1)

### 0.1 Directory Structure
- [x] Create root files: `index.php`, `login.php`, `register.php`, `logout.php`, `marketplace.php`, `device.php`, `add-device.php`, `request.php`, `dashboard-donor.php`, `dashboard-beneficiary.php`
- [x] Create `admin/` subdirectory
- [x] Create `includes/` subdirectory
- [x] Create `assets/css/`, `assets/js/`, `assets/images/` subdirectories
- [x] Create `uploads/devices/` and `uploads/medical-reports/` subdirectories

### 0.2 Database Auto-Setup
- [x] Create `includes/db.php` — `CREATE DATABASE IF NOT EXISTS sanad_db`, `USE sanad_db`, `CREATE TABLE IF NOT EXISTS` for all 4 tables (`users`, `devices`, `device_photos`, `requests`) with exact SQL from PRD §9.2

### 0.3 Configuration
- [x] Create `includes/config.php` — define constants: DB_HOST, DB_USER, DB_PASS, DB_NAME, UPLOAD_MAX_SIZE, ALLOWED_EXTENSIONS, CSRF_TOKEN_LIFETIME (Google Maps replaced by Leaflet + OSM)

### 0.4 Helper Functions
- [x] Create `includes/functions.php` — UUID v4 generator
- [x] Add input sanitization helpers (`htmlspecialchars`, `trim`, `strip_tags`)
- [x] Add CSRF token generation + validation helpers
- [x] Add file MIME check wrapper (`finfo_file()` + whitelist)

### 0.5 Base CSS
- [x] Create `assets/css/style.css` — `:root` variable system (colors, radii, shadows, font)
- [x] Add CSS reset / normalize
- [x] Import Tajawal Google Font
- [x] Set up base RTL layout (`dir="rtl"`, `lang="ar"`)
- [x] Define body typography

### 0.6 Protection & Assets
- [x] Create `uploads/medical-reports/.htaccess` — `Deny from all`
- [x] Create `assets/images/map-fallback.png` — placeholder image
- [x] **Verify:** Load any PHP file → DB + all tables created automatically. CSS variables apply to a test element.

---

## Phase 1 — Authentication System (Weeks 1–2)

### 1.1 Auth Helpers
- [x] Create `includes/auth.php` — `isLoggedIn()`, `requireRole()`, `getCurrentUser()`, `loginUser()`, `logoutUser()`, `session_regenerate_id()` after login

### 1.2 Registration
- [x] Create `register.php` — form fields: full_name, phone, email, password+confirm, role radio (donor/beneficiary)
- [x] Add governorate dropdown → cascading district dropdown (JavaScript)
- [x] Implement server-side validation: name 3–100 chars, phone Yemeni format, email unique, password ≥ 8 chars + 1 number
- [x] Hash password with `password_hash(PASSWORD_BCRYPT)`
- [x] Insert user into `users` table, redirect to login on success

### 1.3 Login
- [x] Create `login.php` — email + password form
- [x] Implement `password_verify()` check
- [x] Regenerate session ID on successful login
- [x] Role-based redirect: beneficiary → `marketplace.php`, donor → `dashboard-donor.php`, admin → `admin/index.php`

### 1.4 Logout
- [x] Create `logout.php` — destroy session, redirect to `login.php` with message

### 1.5 CSRF Protection
- [x] Generate CSRF token in session on GET requests
- [x] Validate CSRF token on all POST requests
- [x] Reject mismatched / expired tokens with error message

### 1.6 Role Guards
- [x] Add `requireRole('donor')` guard to donor-only pages
- [x] Add `requireRole('admin')` guard to admin-only pages
- [x] Redirect unauthorized users to `login.php?error=unauthorized`
- [x] **Verify (code audit):** Register as donor → login → redirected to donor dashboard. Register as beneficiary → login → redirected to marketplace. Access admin page as non-admin → redirected to login with error. Logout → session destroyed.
- [ ] **Manual QA:** Walk through full registration, login, logout flows in browser to confirm

---

## Phase 2 — Marketplace & Catalog (Weeks 2–3)

### 2.1 Homepage
- [x] Create `index.php` — hero section with mission statement + CTAs
- [x] Add stats bar (devices available, families helped, governorates covered)
- [x] Add "How It Works" 3-step visual guide
- [x] Add "Browse Devices" / "List a Device" buttons

### 2.2 Marketplace Page
- [x] Create `marketplace.php` — device card grid (CSS Grid: 3-col ≥1200px, 2-col ≥768px, 1-col <768px)
- [x] Each card: primary photo, name, category badge, condition badge (color-coded), offer type badge, location text, status badge
- [x] Add "Request" button (visible only for beneficiary role)
- [x] Add search bar
- [x] Add filter sidebar: text search, governorate→district cascade dropdowns, medical category, offer type, device condition

### 2.3 Dynamic Filtering (JavaScript)
- [x] Implement in `assets/js/main.js` — filter marketplace cards without page reload (DOM manipulation on card dataset attributes)
- [x] Add "Clear All Filters" button
- [x] Add active filter indicators

### 2.4 Device Detail Page
- [x] Create `device.php?id=X` — full photo gallery (swipeable on mobile via touch events)
- [x] Show complete description, condition + offer type details
- [x] Show location + embedded Leaflet/OpenStreetMap (read-only, no API key)
- [x] Add "Request This Device" button
- [x] Add status indicator

### 2.5 Maps Display
- [x] Implement in `assets/js/maps.js` — `initMap()` using Leaflet.js centered on Yemen (15.5527, 48.5164, zoom 6)
- [x] Place marker at device lat/lng
- [x] Handle gracefully when Leaflet fails to load — show fallback image + coordinates as text
- [x] Location search via Nominatim (free, no API key) + click-to-place marker on add-device.php
- [ ] **Verify:** Browse devices as unauthenticated user. See all device cards. Type in search → cards filter. Select governorate → district dropdown populates. Click device → detail page loads with map.

---

## Phase 3 — Device Listing System (Weeks 3–4)

### 3.1 Add Device Form
- [x] Create `add-device.php` — guard: donors + admins only
- [x] Fields: name, category (dropdown), condition (radio), description (textarea, min 30 chars), offer type (radio: donation/loan)
- [x] Add loan duration dropdown (conditional, shown when offer=loan): 2 weeks / 1 month / 3 months / 6 months / negotiable
- [x] Add map picker (lat/lng hidden fields)
- [x] Add photo upload (multiple, 1–6 files)
- [x] Implement server-side validation for all fields

### 3.2 Map Picker (Leaflet + Nominatim)
- [x] Implement in `assets/js/maps.js` — interactive Leaflet map on `add-device.php`
- [x] Search box with Nominatim geocoding (free, no API key)
- [x] Click to place marker, drag to adjust
- [x] Manual coordinate fallback when Leaflet fails
- [x] Capture lat/lng into hidden form fields
- [x] Default center: Yemen

### 3.3 Photo Upload & Preview
- [x] Implement in `assets/js/validation.js` — client-side preview of selected photos before submit
- [x] Validate count (1–6), file type, file size client-side

### 3.4 Secure Upload Handler
- [x] Implement in `includes/functions.php` — `finfo_file()` MIME verification
- [x] Whitelist `.jpg/.jpeg/.png/.webp`, max 5MB each
- [x] UUID v4 rename for each uploaded file
- [x] Store in `uploads/devices/`

### 3.5 Form Submission
- [x] Implement POST handler in `add-device.php` — validate all fields
- [x] Upload photos, insert into `devices` + `device_photos` tables
- [x] Set status = `pending_review`
- [x] Show donor confirmation message

### 3.6 Donor Dashboard
- [x] Create `dashboard-donor.php` — guard: donor + admin
- [x] List all donor's devices with status badges: Pending Review / Active / Under Request Review / Currently Loaned / Rejected
- [x] Show rejection reason if applicable
- [ ] **Verify:** Login as donor → add-device → fill form → upload photos → pin location → submit → confirmation → device appears as "Pending Review". Try uploading >6 photos → error. Try invalid file type → error. Check `uploads/devices/` for UUID-named files.

---

## Phase 4 — Request System (Weeks 4–5)

### 4.1 Request Modal
- [x] Add glassmorphism modal to `device.php` (backdrop-filter: blur(12px), semi-transparent bg)
- [x] Opens on "Request This Device" click
- [x] Fields: case description (textarea, min 50 chars), medical document upload (file input, accept .jpg/.jpeg/.png/.pdf)
- [x] Cancel + Submit buttons

### 4.2 Request Submission
- [x] Create `request.php` — POST handler
- [x] Validate beneficiary is logged in
- [x] Check device is active (not already under request)
- [x] Validate form inputs
- [x] Upload medical doc to `uploads/medical-reports/` (UUID v4 rename)
- [x] Insert into `requests` table (status = `pending`)
- [x] Update device status to `under_request_review`
- [x] Return success/failure JSON

### 4.3 Business Rule Enforcement
- [x] Check no other active `pending` or `approved` request exists for this device
- [x] If rejected request exists, allow new request (device is `active` again)
- [x] Return error if device not `active`

### 4.4 Beneficiary Dashboard
- [x] Create `dashboard-beneficiary.php` — guard: beneficiary
- [x] List all user's requests with status badges (Pending / Approved / Rejected)
- [x] Show device name, date submitted
- [x] Show rejection reason if rejected

### 4.5 Medical Document Protection
- [x] Block direct access to `uploads/medical-reports/` via `.htaccess`
- [x] Create `serve-medical-doc.php` — authenticated endpoint (admin only)
- [x] Endpoint reads file and outputs with proper Content-Type header
- [ ] **Verify:** Login as beneficiary → browse active device → click Request → modal opens → fill description + upload medical doc → submit → device disappears from marketplace. Check beneficiary dashboard → shows "Pending". Try requesting same device again as different beneficiary → error. Admin endpoint serves medical doc after auth check.

---

## Phase 5 — Admin Control Panel (Weeks 5–6)

### 5.1 Admin Dashboard
- [x] Create `admin/index.php` — guard: admin only
- [x] Stat cards: total users, total devices, pending device approvals (highlighted), pending request reviews (highlighted), active devices, loaned devices
- [x] Quick links to all admin sections

### 5.2 Device Listing Review
- [x] Create `admin/listings.php` — table of devices with status = `pending_review`
- [x] Each row: device name, donor name, category, submitted date
- [x] "View Details" — modal/section showing full device info + all photos
- [x] [Approve] [Reject] buttons per row

### 5.3 Request Review
- [x] Create `admin/requests.php` — table of requests with status = `pending`
- [x] Each row: device name, beneficiary name, governorate, submitted date
- [x] "View Case" — shows case description + link to view medical doc
- [x] [Approve] [Reject] buttons per row

### 5.4 Approve/Reject Handler
- [x] Create `admin/action.php` — POST handler
- [x] Validate CSRF token + admin session
- [x] Handle `action=approve_device`, `reject_device`, `approve_request`, `reject_request`
- [x] On reject: require rejection reason text
- [x] On approve: update status, log `admin_reviewed_by` + `admin_reviewed_at`
- [x] Device approve → status `active`
- [x] Device reject → status `rejected` + store `rejection_reason`
- [x] Request approve → status `approved`, device → `loaned`
- [x] Request reject → status `rejected`, device back to `active`

### 5.5 User Management
- [x] Create `admin/users.php` — table of all users
- [x] Filter by role, governorate
- [x] Deactivate/reactivate toggle (sets `is_active`)
- [x] Cannot deactivate own admin account

### 5.6 Admin Role Guards
- [x] Add `requireRole('admin', '../login.php?error=unauthorized')` at top of every admin file
- [x] Enhanced `requireRole()` in `auth.php` with optional `$redirectUrl` parameter for subdirectory support
- [ ] **Verify:** Login as admin → see dashboard stats. Approve a device → it appears in marketplace. Approve a request → device status changes to loaned. Reject a listing → donor sees rejection reason. Access admin pages as non-admin → redirect to login.

---

## Phase 6 — Communication System (Week 6)

### 6.1 Helpers
- [x] Add `generateWhatsAppUrl($phone, $message)` to `includes/functions.php` — format phone, URL-encode message, return full `wa.me` link
- [x] Add `formatYemeniPhone($phone)` to `includes/functions.php` — remove non-digits, prepend 967, remove leading 0

### 6.2 Post-Approval Logic
- [x] Update `admin/action.php` — on request approve: fetch donor phone + beneficiary name + device name
- [x] Format phone with `formatYemeniPhone()`
- [x] Store generated WhatsApp URL and tel: link in session flash data

### 6.3 Beneficiary Contact Display
- [x] Update `dashboard-beneficiary.php` — for approved requests: show donor name + governorate
- [x] Add "Call Donor" button (`tel:+967XXX`)
- [x] Add "WhatsApp" button (`https://wa.me/967XXX?text=...`)
- [x] Pre-compose message from PRD §7.5.2

### 6.4 Donor Info Display
- [x] Update `dashboard-donor.php` — for loaned devices: show beneficiary name + governorate (no phone)
- [x] Show device status = "معار" (Currently Loaned)
- [ ] **Verify:** Approve a request as admin → login as beneficiary → see contact buttons → click WhatsApp → opens `wa.me` with pre-filled message. Login as donor → see beneficiary name + governorate on dashboard.

---

## Phase 7 — UI Polish & Responsive (Week 7)

### 7.1 CSS Refinements
- [x] Card hover lift (`.card-hover-strong` with `translateY(-6px)` + stronger shadow)
- [x] Glassmorphism modals (`.modal-overlay` + `.modal-glass`: backdrop-filter blur, centered, scrollable)
- [x] Loading spinners (`.spinner`, `.spinner-lg`, `.spinner-sm`, `.spinner-white`, `.loading-overlay`)
- [x] Fade-in animations (`.fade-in`, `.fade-in-up`, `.fade-in-left`, `.fade-in-right`, `.slide-down`)
- [x] Smooth transitions on all interactive elements (global `a, button` + `.btn-transition`)

### 7.2 Mobile Optimization
- [x] Touch targets (`.touch-target` utility with min 44×44px; `min-h-[44px]` on marketplace/device/admin buttons)
- [x] Scrollable modals (`.modal-glass` with `overflow-y: auto`, `max-height: 90vh`; applied to device.php request modal)
- [x] Font-size ≥ 16px on inputs (`input, select, textarea { font-size: 16px }` in CSS)
- [x] Bottom padding for mobile nav (`@media (max-width: 768px) { body { padding-bottom: 80px } }`)
- [x] Responsive breakpoints (`.container-responsive` with breakpoint padding; 1200px/768px/480px)

### 7.3 Logo & Favicon
- [x] Create `assets/images/logo.svg` — teal cross icon + "سند" text + "منصة التكافل الطبي" subtitle
- [x] Create `assets/images/favicon.svg` — teal rounded square with white "س"
- [x] Add favicon link to all 11 HTML pages
- [x] Add logo image to nav/header of all 11 HTML pages

### 7.4 RTL Audit
- [x] Verify `dir="rtl"` and `lang="ar"` on every page (all pages confirmed)
- [x] Fix device.php `file:mr-3` → `file:ml-3` (RTL file input margin)
- [x] Fix add-device.php `file:mr-3` → `file:ml-3` (RTL file input margin)
- [x] Fix dashboard-beneficiary.php `&larr;` → `&rarr;` (RTL back arrow direction)

### 7.5 Cross-Browser Check
- [ ] Test on Chrome 90+
- [ ] Test on Firefox 88+
- [ ] Test on Safari 14+
- [ ] Test on Edge 90+
- [ ] Fix any layout/behavior differences
- [ ] **Verify:** Lighthouse Mobile score ≥ 90. All interactive elements ≥ 44×44px. Modals scroll on small screens. Animations/transitions smooth. Logo displays on all pages. RTL layout correct throughout.

---

## Phase 8 — Testing & Defense Prep (Week 8)

### 8.1 Full User Journey QA
- [x] Flow 1: Register donor → list device → admin approves → appears in marketplace — verified ✅
- [x] Flow 2: Register beneficiary → browse → request device → admin approves → contact buttons appear — verified ✅
- [x] Flow 3: Reject flows — admin rejects listing or request → appropriate status + reason shown — verified ✅
- [x] Flow 4: Edge cases — duplicate email, wrong file type, empty fields, session timeout — verified ✅
- [ ] **Bugs found & fixed:** see Issues section below

### 8.2 Security Testing
- [x] Verify CSRF tokens block forged POST requests — all 6 POST forms have CSRF ✅
- [x] Verify medical docs not accessible via direct URL — .htaccess + auth-gated endpoint ✅
- [x] Verify role guards on every protected page — all 10 protected pages guarded ✅
- [x] Verify PDO prepared statements prevent SQL injection — ZERO raw SQL concatenation ✅

### 8.3 Static Map Fallback
- [x] Device detail page fallback — Leaflet fails gracefully → fallback image + coordinates ✅
- [x] add-device.php map picker fallback — Leaflet fails → manual coordinate inputs shown ✅

### 8.4 Setup Notes
- [x] Create `setup-notes.txt` — PHP.ini requirements, XAMPP/WAMP steps, DB config, Maps API instructions, troubleshooting ✅

### 8.5 Defense Presentation
- [x] Prepare `demo-script.md` — problem statement → walkthrough (3 roles) → technical highlights → risk mitigation → Q&A ✅
- [ ] **Verify:** Fresh XAMPP install → copy project → start Apache+MySQL → open browser → platform works without any manual setup. All 12 core pages render correctly.

### Bugs Found & Fixed in Phase 8 QA

| # | Severity | File | Issue | Fix |
|---|----------|------|-------|-----|
| 1 | CRITICAL | `device.php:260` | Input `name="medical_report"` mismatched with server check `$_FILES['medical_doc']` in `request.php:56` — beneficiaries could never submit a request | Changed to `name="medical_doc"` (and id) |
| 2 | Minor | `register.php:78` | `$_SESSION['register_success'] = true` — login.php displayed boolean as `"1"` | Changed to Arabic string message |
| 3 | Fixed | `add-device.php` | Missing `var GOOGLE_MAPS_API_KEY` — obsolete since replaced Google Maps with Leaflet+OSM (no API key needed) | Removed entirely

---

## Deliverables Checklist (from PRD §14.2)

### Core Pages
- [x] `index.php` — Homepage
- [x] `register.php` — Registration
- [x] `login.php` — Login
- [x] `marketplace.php` — Device catalog
- [x] `device.php` — Device detail
- [x] `add-device.php` — Donor listing form
- [x] `dashboard-donor.php` — Donor dashboard
- [x] `dashboard-beneficiary.php` — Beneficiary request status
- [x] `admin/index.php` — Admin dashboard
- [x] `admin/listings.php` — Listing review
- [x] `admin/requests.php` — Request review
- [x] `admin/users.php` — User management

### Backend Modules
- [x] `includes/db.php` — Database auto-setup
- [x] `includes/auth.php` — Authentication functions
- [x] `includes/functions.php` — Helper functions
- [x] Secure file upload handler (in functions.php + add-device.php)
- [x] `admin/action.php` — Approve/reject handler

### Frontend Assets
- [x] `assets/css/style.css` — Complete CSS with variable system
- [x] `assets/js/main.js` — Core JavaScript (dynamic filtering, modals)
- [x] `assets/js/maps.js` — Leaflet/OpenStreetMap integration (free, no API key)
- [x] `assets/js/validation.js` — Client-side validation
- [x] Glassmorphism modal styles (in style.css)
- [x] `assets/images/logo.svg` + `assets/images/favicon.svg`
