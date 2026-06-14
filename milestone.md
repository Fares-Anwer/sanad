# Milestones — Sanad Platform Project Plan

**Project:** Sanad (سَنَد) — Medical Equipment Solidarity Platform  
**Stack:** Pure PHP + Vanilla JS + CSS3 + MySQL (XAMPP/WAMP)  
**Duration:** 8 Weeks (8 phases)  
**Source:** PRD.md v1.0.0

---

## Dependency Graph

```
Phase 0 ─┬─► Phase 1 ─┬─► Phase 2 ─┬─► Phase 4 ─┬─► Phase 5 ─┬─► Phase 6 ─┬─► Phase 7 ─► Phase 8
          │            │            │             │             │             │
          │            ├─► Phase 3 ─┤             │             │             │
          │            │                         │             │             │
          └────────────┘                         └─────────────┘             │
                                                                              │
                                                                              ▼
                                                                         Done
```

Phases within the same week can be parallelized. Each phase depends on all earlier phases to its left.

---

## Phase 0 — Setup & Architecture (Week 1)

**Objective:** Scaffold the entire project structure, establish design tokens, and build the failsafe database auto-setup.

| Deliverable | Files | Details |
|-------------|-------|---------|
| Directory structure | All files under `/sanad/` | Create every directory and file stub from PRD §8.3 layout (root pages, admin/, includes/, assets/css/, assets/js/, assets/images/, uploads/devices/, uploads/medical-reports/) |
| Database auto-setup | `includes/db.php` | Connect with no DB selected → `CREATE DATABASE IF NOT EXISTS sanad_db` → `USE sanad_db` → `CREATE TABLE IF NOT EXISTS` for all 4 tables (`users`, `devices`, `device_photos`, `requests`) using exact SQL from PRD §9.2 |
| Configuration | `includes/config.php` | Define constants: DB_HOST, DB_USER, DB_PASS, DB_NAME, UPLOAD_MAX_SIZE, ALLOWED_EXTENSIONS, GOOGLE_MAPS_API_KEY, CSRF_TOKEN_LIFETIME |
| Helper functions | `includes/functions.php` | UUID v4 generator, input sanitization (`htmlspecialchars`, `trim`, `strip_tags`), CSRF token generation + validation, file MIME check wrapper |
| Base CSS | `assets/css/style.css` | Full `:root` variable system from PRD §11.2 (colors, radii, shadows, font), reset/normalize, Tajawal Google Font import, base RTL layout (`dir="rtl"`, `lang="ar"`), body typography |
| Upload dirs protection | `uploads/medical-reports/.htaccess` | `Deny from all` to block direct URL access |
| Static map fallback | `assets/images/map-fallback.png` | Placeholder image for defense demo if Google Maps API key unavailable |

**Verification:** Load any PHP file in browser → DB + all tables created automatically. CSS variables apply to a test element.

**Risk:** Google Maps API key not available during defense — prepare static fallback (`assets/images/map-fallback.png`) and show lat/lng as text.

---

## Phase 1 — Authentication System (Weeks 1–2)

**Objective:** Full registration, login, logout, session management, and role-based access control.

| Deliverable | Files | Details |
|-------------|-------|---------|
| Auth helpers | `includes/auth.php` | `isLoggedIn()`, `requireRole('donor')`, `getCurrentUser()`, `loginUser()`, `logoutUser()`, `session_regenerate_id()` after login |
| Registration | `register.php` | Full form: full_name, phone, email, password+confirm, role radio (donor/beneficiary), governorate dropdown → cascading district dropdown (JS). Validation: name 3-100 chars, phone Yemeni format, email unique, password ≥ 8 chars + 1 number. `password_hash(PASSWORD_BCRYPT)` |
| Login | `login.php` | Email + password form. `password_verify()`. Regenerate session ID. Role-based redirect: beneficiary → `marketplace.php`, donor → `dashboard-donor.php`, admin → `admin/index.php` |
| Logout | `logout.php` | Destroy session, redirect to login with message |
| CSRF protection | All POST forms | Generate token in session on GET, validate on POST. Reject mismatched/expired tokens |
| Role guard pattern | Top of every protected page | `requireRole('donor')` or `requireRole('admin')` — redirect unauthorized to `login.php?error=unauthorized` |

**Verification:** Register as donor → login → redirected to donor dashboard. Register as beneficiary → login → redirected to marketplace. Access admin page as non-admin → redirected to login with error. Logout → session destroyed.

---

## Phase 2 — Marketplace & Catalog (Weeks 2–3)

**Objective:** Build the public-facing homepage, device marketplace with live filtering, and device detail page.

| Deliverable | Files | Details |
|-------------|-------|---------|
| Homepage | `index.php` | Hero section with mission statement + CTAs, stats bar (devices available, families helped, governorates covered), "How It Works" 3-step visual guide, "Browse Devices" / "List a Device" buttons |
| Marketplace | `marketplace.php` | Device card grid (CSS Grid: 3-col ≥1200px, 2-col ≥768px, 1-col <768px). Each card: primary photo, name, category badge, condition badge (color-coded), offer type badge, location text, status badge, "Request" button (visible only for beneficiary). Search bar + filter sidebar: text search, governorate→district cascade dropdowns, medical category, offer type, device condition |
| Dynamic filtering | `assets/js/main.js` | Filter marketplace cards without page reload (DOM manipulation on card dataset attributes). "Clear All Filters" button. Active filter indicators |
| Device detail | `device.php?id=X` | Full photo gallery (swipeable on mobile via touch events), complete description, condition + offer type details, location + embedded Google Map (read-only), "Request This Device" button, status indicator |
| Maps display | `assets/js/maps.js` | `initMap()` centered on Yemen (15.5527, 48.5164, zoom 6). Place marker at device lat/lng. Handle missing API key gracefully (show fallback image) |

**Verification:** Browse devices as unauthenticated user. See all device cards. Type in search → cards filter. Select governorate → district dropdown populates. Click device → detail page loads with map.

---

## Phase 3 — Device Listing System (Weeks 3–4)

**Objective:** Allow donors to list medical devices with photos, map pin, and conditional loan terms.

| Deliverable | Files | Details |
|-------------|-------|---------|
| Add device form | `add-device.php` | Guard: donors + admins only. Fields: name, category (dropdown), condition (radio), description (textarea, min 30 chars), offer type (radio: donation/loan), loan duration (conditional dropdown when offer=loan: 2 weeks / 1 month / 3 months / 6 months / negotiable), map picker (lat/lng hidden fields), photo upload (multiple, 1-6 files) |
| Map picker | `assets/js/maps.js` | Interactive map on add-device.php. Click to place/remove marker. Capture lat/lng into hidden form fields. Default center: Yemen |
| Photo upload & preview | `assets/js/validation.js` | Client-side: preview selected photos before submit. Validate count (1-6), file type, file size client-side |
| Secure upload handler | `includes/functions.php` | Server-side: `finfo_file()` MIME verification, whitelist `.jpg/.jpeg/.png/.webp`, max 5MB each, UUID v4 rename, store in `uploads/devices/` |
| Form submission | `add-device.php` (POST) | Validate all fields, upload photos, insert into `devices` + `device_photos` tables, status = `pending_review`. Donor sees confirmation message |
| Donor dashboard | `dashboard-donor.php` | Guard: donor + admin. List all donor's devices with status badges: Pending Review / Active / Under Request Review / Currently Loaned / Rejected. Show rejection reason if applicable |

**Verification:** Login as donor → navigate to add-device → fill form → upload photos → pin location → submit → see confirmation → device appears in dashboard as "Pending Review". Try uploading >6 photos → error. Try invalid file type → error. Check uploads/devices/ for UUID-named files.

---

## Phase 4 — Request System (Weeks 4–5)

**Objective:** Enable beneficiaries to request devices with medical proof, and track request status.

| Deliverable | Files | Details |
|-------------|-------|---------|
| Request modal | `device.php` | Glassmorphism modal (backdrop-filter: blur(12px), semi-transparent bg). Opens on "Request This Device" click. Fields: case description (textarea, min 50 chars), medical document upload (file input, accept .jpg/.jpeg/.png/.pdf). Cancel + Submit buttons |
| Request submission | `request.php` | POST handler. Validate beneficiary is logged in. Check device is active (not already under request). Validate form inputs. Upload medical doc to `uploads/medical-reports/` (UUID rename). Insert into `requests` table (status = `pending`). Update device status to `under_request_review`. Return success/failure JSON |
| Business rule enforcement | `request.php` | Check no other active `pending` or `approved` request exists for this device. If rejected request exists, allow new request (device is `active` again). Return error if device not `active` |
| Beneficiary dashboard | `dashboard-beneficiary.php` | Guard: beneficiary. List all user's requests with status badges (Pending / Approved / Rejected), device name, date submitted. Show rejection reason if rejected |
| Medical doc protection | `.htaccess` + serve script | Block direct access to `uploads/medical-reports/`. Create `serve-medical-doc.php` — authenticated endpoint (admin only) that reads file and outputs with proper Content-Type header |

**Verification:** Login as beneficiary → browse active device → click Request → modal opens → fill description + upload medical doc → submit → device disappears from marketplace. Check beneficiary dashboard → shows "Pending". Try requesting same device again as different beneficiary → error. Admin endpoint serves medical doc after auth check.

---

## Phase 5 — Admin Control Panel (Weeks 5–6)

**Objective:** Full governance interface for admins to manage devices, requests, and users.

| Deliverable | Files | Details |
|-------------|-------|---------|
| Admin dashboard | `admin/index.php` | Guard: admin only. Stat cards: total users, total devices, pending device approvals (highlighted), pending request reviews (highlighted), active devices, loaned devices. Quick links to all admin sections |
| Device listing review | `admin/listings.php` | Table of devices with status = `pending_review`. Each row: device name, donor name, category, submitted date, "View Details" (modal/section showing full device info + all photos) + [Approve] [Reject] buttons |
| Request review | `admin/requests.php` | Table of requests with status = `pending`. Each row: device name, beneficiary name, governorate, submitted date, "View Case" (shows case description + link to view medical doc) + [Approve] [Reject] buttons |
| Approve/Reject handler | `admin/action.php` | POST handler. Validate CSRF token + admin session. Handle `action=approve_device`, `reject_device`, `approve_request`, `reject_request`. On reject: require rejection reason text. On approve: update status, log `admin_reviewed_by` + `admin_reviewed_at`. Device approve → status `active`. Device reject → status `rejected` + `rejection_reason`. Request approve → status `approved`, device → `loaned`. Request reject → status `rejected`, device back to `active` |
| User management | `admin/users.php` | Table of all users. Filter by role, governorate. Deactivate/reactivate toggle (sets `is_active`). Cannot deactivate own admin account |
| Role guard | Top of every admin file | `requireRole('admin')` — must come before any output |

**Verification:** Login as admin → see dashboard stats. Navigate to listings → approve a device → it appears in marketplace. Navigate to requests → approve a request → device status changes to loaned. Reject a listing → donor can see rejection reason. Try accessing admin pages as non-admin → redirect to login.

---

## Phase 6 — Communication System (Week 6)

**Objective:** After admin approval, reveal contact information and generate WhatsApp/call links.

| Deliverable | Files | Details |
|-------------|-------|---------|
| Post-approval logic | `admin/action.php` | On request approve: fetch donor phone + beneficiary name + device name. Format phone: strip non-digits, prepend 967, ltrim 0. Store generated WhatsApp URL and tel: link in session or pass to dashboard pages |
| Beneficiary contact display | `dashboard-beneficiary.php` | For approved requests: show donor name + governorate, "Call Donor" button (`tel:+967XXX`), "WhatsApp" button (`https://wa.me/967XXX?text=...`). Pre-composed message from PRD §7.5.2 |
| Donor info display | `dashboard-donor.php` | For loaned devices: show beneficiary name + governorate (no phone). Device status = "Currently Loaned" |
| WhatsApp URL generation | `includes/functions.php` | Helper: `generateWhatsAppUrl($phone, $message)` — format phone, URL-encode message, return full `wa.me` link. Message template in Arabic from PRD |
| Phone formatting helper | `includes/functions.php` | Helper: `formatYemeniPhone($phone)` — remove non-digits, prepend 967 country code, remove leading 0 |

**Verification:** Approve a request as admin → login as beneficiary → see contact buttons for that request → click WhatsApp → opens wa.me with pre-filled message. Login as donor → see beneficiary name + governorate on dashboard.

---

## Phase 7 — UI Polish & Responsive (Week 7)

**Objective:** Refine all visual elements, ensure mobile-friendliness, and polish animations.

| Deliverable | Files | Details |
|-------------|-------|---------|
| CSS refinements | `assets/css/style.css` | Card hover lift (`transform: translateY(-4px)` + shadow transition). Glassmorphism modals completed. Loading spinners. Fade-in animations on page load. Smooth transitions on all interactive elements |
| Mobile optimization | `assets/css/style.css` | Touch targets ≥ 44×44px. Scrollable modals (overflow-y: auto). Font-size ≥ 16px on inputs (prevents iOS zoom). Bottom padding for mobile nav. Responsive breakpoints verified |
| Logo + favicon | `assets/images/logo.svg`, `favicon.ico` | Design platform logo (Sanad branding, teal/white). Generate favicon |
| RTL audit | All pages | Verify `dir="rtl"` and `lang="ar"` on every page. Check text alignment, icon placement, form layout in RTL |
| Cross-browser check | Manual | Test on Chrome 90+, Firefox 88+, Safari 14+, Edge 90+. Fix any layout/behavior differences |

**Verification:** Lighthouse Mobile score ≥ 90. All interactive elements ≥ 44×44px. Modals scroll on small screens. Animation/transitions smooth. Logo displays on all pages. RTL layout correct throughout.

---

## Phase 8 — Testing & Defense Prep (Week 8)

**Objective:** Full QA, documentation, and defense readiness.

| Deliverable | Details |
|-------------|---------|
| Full user journey QA | Walk through all flows: (1) Register donor → list device → admin approves → appears in marketplace. (2) Register beneficiary → browse → request device → admin approves → contact buttons appear. (3) Reject flows: admin rejects listing or request → appropriate status + reason shown. (4) Edge cases: duplicate email, wrong file type, empty fields, session timeout |
| Security testing | Verify CSRF tokens block forged POST. Verify medical docs not accessible via direct URL. Verify role guards on every protected page. Verify PDO prepared statements prevent SQL injection |
| Static map fallback | Test device detail page without Google Maps API key — fallback image + coordinates display correctly |
| Setup notes | Create `setup-notes.txt`: PHP.ini requirements (upload_max_filesize, post_max_size, max_execution_time), XAMPP/WAMP setup steps, database credentials configuration, Google Maps API key instructions |
| Defense presentation | Prepare demo script covering: problem statement → platform walkthrough (all 3 roles) → technical highlights (auto-setup DB, secure uploads, no frameworks) → risk mitigation → Q&A readiness |

**Verification:** Fresh XAMPP install → copy project → start Apache+MySQL → open browser → platform works without any manual setup. All 12 pages from PRD §14.2 deliverable checklist render correctly.

---

## Deliverables Checklist (from PRD §14.2)

### Core Pages
- [ ] `index.php` — Homepage
- [ ] `register.php` — Registration
- [ ] `login.php` — Login
- [ ] `marketplace.php` — Device catalog
- [ ] `device.php` — Device detail
- [ ] `add-device.php` — Donor listing form
- [ ] `dashboard-donor.php` — Donor dashboard
- [ ] `dashboard-beneficiary.php` — Beneficiary request status
- [ ] `admin/index.php` — Admin dashboard
- [ ] `admin/listings.php` — Listing review
- [ ] `admin/requests.php` — Request review
- [ ] `admin/users.php` — User management

### Backend Modules
- [ ] `includes/db.php` — Database auto-setup
- [ ] `includes/auth.php` — Authentication functions
- [ ] `includes/functions.php` — Helper functions
- [ ] Secure file upload handler (in functions.php or dedicated)
- [ ] `admin/action.php` — Approve/reject handler

### Frontend Assets
- [ ] `assets/css/style.css` — Complete CSS with variable system
- [ ] `assets/js/main.js` — Core JavaScript (dynamic filtering, modals)
- [ ] `assets/js/maps.js` — Google Maps integration
- [ ] `assets/js/validation.js` — Client-side validation
- [ ] Glassmorphism modal styles (in style.css)
- [ ] `assets/images/logo.svg` + `favicon.ico`

---

## Risk Notes per Phase

| Phase | Risk | Mitigation |
|-------|------|------------|
| 0, 2 | Google Maps API key unavailable | Static map fallback image + text coordinates |
| 0 | DB import fails on defense machine | Failsafe auto-setup eliminates manual import |
| 3, 4 | File upload size exceeded | Document PHP.ini requirements in setup-notes.txt |
| 3, 4 | Slow internet during defense | All core functionality works locally on XAMPP; WhatsApp demo on phone |
| all | Scope creep | Strictly adhere to V1.0 scope defined in PRD |
| 4, 5 | Medical report privacy breach | Serve via authenticated PHP endpoint, never direct URL; .htaccess deny |
| 7, 8 | Mobile layout issues | DevTools mobile simulation; Lighthouse audit |
