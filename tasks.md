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
- [ ] Create root files: `index.php`, `login.php`, `register.php`, `logout.php`, `marketplace.php`, `device.php`, `add-device.php`, `request.php`, `dashboard-donor.php`, `dashboard-beneficiary.php`
- [ ] Create `admin/` subdirectory
- [ ] Create `includes/` subdirectory
- [ ] Create `assets/css/`, `assets/js/`, `assets/images/` subdirectories
- [ ] Create `uploads/devices/` and `uploads/medical-reports/` subdirectories

### 0.2 Database Auto-Setup
- [ ] Create `includes/db.php` — `CREATE DATABASE IF NOT EXISTS sanad_db`, `USE sanad_db`, `CREATE TABLE IF NOT EXISTS` for all 4 tables (`users`, `devices`, `device_photos`, `requests`) with exact SQL from PRD §9.2

### 0.3 Configuration
- [ ] Create `includes/config.php` — define constants: DB_HOST, DB_USER, DB_PASS, DB_NAME, UPLOAD_MAX_SIZE, ALLOWED_EXTENSIONS, GOOGLE_MAPS_API_KEY, CSRF_TOKEN_LIFETIME

### 0.4 Helper Functions
- [ ] Create `includes/functions.php` — UUID v4 generator
- [ ] Add input sanitization helpers (`htmlspecialchars`, `trim`, `strip_tags`)
- [ ] Add CSRF token generation + validation helpers
- [ ] Add file MIME check wrapper (`finfo_file()` + whitelist)

### 0.5 Base CSS
- [ ] Create `assets/css/style.css` — `:root` variable system (colors, radii, shadows, font)
- [ ] Add CSS reset / normalize
- [ ] Import Tajawal Google Font
- [ ] Set up base RTL layout (`dir="rtl"`, `lang="ar"`)
- [ ] Define body typography

### 0.6 Protection & Assets
- [ ] Create `uploads/medical-reports/.htaccess` — `Deny from all`
- [ ] Create `assets/images/map-fallback.png` — placeholder image
- [ ] **Verify:** Load any PHP file → DB + all tables created automatically. CSS variables apply to a test element.

---

## Phase 1 — Authentication System (Weeks 1–2)

### 1.1 Auth Helpers
- [ ] Create `includes/auth.php` — `isLoggedIn()`, `requireRole()`, `getCurrentUser()`, `loginUser()`, `logoutUser()`, `session_regenerate_id()` after login

### 1.2 Registration
- [ ] Create `register.php` — form fields: full_name, phone, email, password+confirm, role radio (donor/beneficiary)
- [ ] Add governorate dropdown → cascading district dropdown (JavaScript)
- [ ] Implement server-side validation: name 3–100 chars, phone Yemeni format, email unique, password ≥ 8 chars + 1 number
- [ ] Hash password with `password_hash(PASSWORD_BCRYPT)`
- [ ] Insert user into `users` table, redirect to login on success

### 1.3 Login
- [ ] Create `login.php` — email + password form
- [ ] Implement `password_verify()` check
- [ ] Regenerate session ID on successful login
- [ ] Role-based redirect: beneficiary → `marketplace.php`, donor → `dashboard-donor.php`, admin → `admin/index.php`

### 1.4 Logout
- [ ] Create `logout.php` — destroy session, redirect to `login.php` with message

### 1.5 CSRF Protection
- [ ] Generate CSRF token in session on GET requests
- [ ] Validate CSRF token on all POST requests
- [ ] Reject mismatched / expired tokens with error message

### 1.6 Role Guards
- [ ] Add `requireRole('donor')` guard to donor-only pages
- [ ] Add `requireRole('admin')` guard to admin-only pages
- [ ] Redirect unauthorized users to `login.php?error=unauthorized`
- [ ] **Verify:** Register as donor → login → redirected to donor dashboard. Register as beneficiary → login → redirected to marketplace. Access admin page as non-admin → redirected to login with error. Logout → session destroyed.

---

## Phase 2 — Marketplace & Catalog (Weeks 2–3)

### 2.1 Homepage
- [ ] Create `index.php` — hero section with mission statement + CTAs
- [ ] Add stats bar (devices available, families helped, governorates covered)
- [ ] Add "How It Works" 3-step visual guide
- [ ] Add "Browse Devices" / "List a Device" buttons

### 2.2 Marketplace Page
- [ ] Create `marketplace.php` — device card grid (CSS Grid: 3-col ≥1200px, 2-col ≥768px, 1-col <768px)
- [ ] Each card: primary photo, name, category badge, condition badge (color-coded), offer type badge, location text, status badge
- [ ] Add "Request" button (visible only for beneficiary role)
- [ ] Add search bar
- [ ] Add filter sidebar: text search, governorate→district cascade dropdowns, medical category, offer type, device condition

### 2.3 Dynamic Filtering (JavaScript)
- [ ] Implement in `assets/js/main.js` — filter marketplace cards without page reload (DOM manipulation on card dataset attributes)
- [ ] Add "Clear All Filters" button
- [ ] Add active filter indicators

### 2.4 Device Detail Page
- [ ] Create `device.php?id=X` — full photo gallery (swipeable on mobile via touch events)
- [ ] Show complete description, condition + offer type details
- [ ] Show location + embedded Google Map (read-only)
- [ ] Add "Request This Device" button
- [ ] Add status indicator

### 2.5 Maps Display
- [ ] Implement in `assets/js/maps.js` — `initMap()` centered on Yemen (15.5527, 48.5164, zoom 6)
- [ ] Place marker at device lat/lng
- [ ] Handle missing API key gracefully — show fallback image + coordinates as text
- [ ] **Verify:** Browse devices as unauthenticated user. See all device cards. Type in search → cards filter. Select governorate → district dropdown populates. Click device → detail page loads with map.

---

## Phase 3 — Device Listing System (Weeks 3–4)

### 3.1 Add Device Form
- [ ] Create `add-device.php` — guard: donors + admins only
- [ ] Fields: name, category (dropdown), condition (radio), description (textarea, min 30 chars), offer type (radio: donation/loan)
- [ ] Add loan duration dropdown (conditional, shown when offer=loan): 2 weeks / 1 month / 3 months / 6 months / negotiable
- [ ] Add map picker (lat/lng hidden fields)
- [ ] Add photo upload (multiple, 1–6 files)
- [ ] Implement server-side validation for all fields

### 3.2 Map Picker
- [ ] Implement in `assets/js/maps.js` — interactive map on `add-device.php`
- [ ] Click to place marker, click again to remove
- [ ] Capture lat/lng into hidden form fields
- [ ] Default center: Yemen

### 3.3 Photo Upload & Preview
- [ ] Implement in `assets/js/validation.js` — client-side preview of selected photos before submit
- [ ] Validate count (1–6), file type, file size client-side

### 3.4 Secure Upload Handler
- [ ] Implement in `includes/functions.php` — `finfo_file()` MIME verification
- [ ] Whitelist `.jpg/.jpeg/.png/.webp`, max 5MB each
- [ ] UUID v4 rename for each uploaded file
- [ ] Store in `uploads/devices/`

### 3.5 Form Submission
- [ ] Implement POST handler in `add-device.php` — validate all fields
- [ ] Upload photos, insert into `devices` + `device_photos` tables
- [ ] Set status = `pending_review`
- [ ] Show donor confirmation message

### 3.6 Donor Dashboard
- [ ] Create `dashboard-donor.php` — guard: donor + admin
- [ ] List all donor's devices with status badges: Pending Review / Active / Under Request Review / Currently Loaned / Rejected
- [ ] Show rejection reason if applicable
- [ ] **Verify:** Login as donor → add-device → fill form → upload photos → pin location → submit → confirmation → device appears as "Pending Review". Try uploading >6 photos → error. Try invalid file type → error. Check `uploads/devices/` for UUID-named files.

---

## Phase 4 — Request System (Weeks 4–5)

### 4.1 Request Modal
- [ ] Add glassmorphism modal to `device.php` (backdrop-filter: blur(12px), semi-transparent bg)
- [ ] Opens on "Request This Device" click
- [ ] Fields: case description (textarea, min 50 chars), medical document upload (file input, accept .jpg/.jpeg/.png/.pdf)
- [ ] Cancel + Submit buttons

### 4.2 Request Submission
- [ ] Create `request.php` — POST handler
- [ ] Validate beneficiary is logged in
- [ ] Check device is active (not already under request)
- [ ] Validate form inputs
- [ ] Upload medical doc to `uploads/medical-reports/` (UUID v4 rename)
- [ ] Insert into `requests` table (status = `pending`)
- [ ] Update device status to `under_request_review`
- [ ] Return success/failure JSON

### 4.3 Business Rule Enforcement
- [ ] Check no other active `pending` or `approved` request exists for this device
- [ ] If rejected request exists, allow new request (device is `active` again)
- [ ] Return error if device not `active`

### 4.4 Beneficiary Dashboard
- [ ] Create `dashboard-beneficiary.php` — guard: beneficiary
- [ ] List all user's requests with status badges (Pending / Approved / Rejected)
- [ ] Show device name, date submitted
- [ ] Show rejection reason if rejected

### 4.5 Medical Document Protection
- [ ] Block direct access to `uploads/medical-reports/` via `.htaccess`
- [ ] Create `serve-medical-doc.php` — authenticated endpoint (admin only)
- [ ] Endpoint reads file and outputs with proper Content-Type header
- [ ] **Verify:** Login as beneficiary → browse active device → click Request → modal opens → fill description + upload medical doc → submit → device disappears from marketplace. Check beneficiary dashboard → shows "Pending". Try requesting same device again as different beneficiary → error. Admin endpoint serves medical doc after auth check.

---

## Phase 5 — Admin Control Panel (Weeks 5–6)

### 5.1 Admin Dashboard
- [ ] Create `admin/index.php` — guard: admin only
- [ ] Stat cards: total users, total devices, pending device approvals (highlighted), pending request reviews (highlighted), active devices, loaned devices
- [ ] Quick links to all admin sections

### 5.2 Device Listing Review
- [ ] Create `admin/listings.php` — table of devices with status = `pending_review`
- [ ] Each row: device name, donor name, category, submitted date
- [ ] "View Details" — modal/section showing full device info + all photos
- [ ] [Approve] [Reject] buttons per row

### 5.3 Request Review
- [ ] Create `admin/requests.php` — table of requests with status = `pending`
- [ ] Each row: device name, beneficiary name, governorate, submitted date
- [ ] "View Case" — shows case description + link to view medical doc
- [ ] [Approve] [Reject] buttons per row

### 5.4 Approve/Reject Handler
- [ ] Create `admin/action.php` — POST handler
- [ ] Validate CSRF token + admin session
- [ ] Handle `action=approve_device`, `reject_device`, `approve_request`, `reject_request`
- [ ] On reject: require rejection reason text
- [ ] On approve: update status, log `admin_reviewed_by` + `admin_reviewed_at`
- [ ] Device approve → status `active`
- [ ] Device reject → status `rejected` + store `rejection_reason`
- [ ] Request approve → status `approved`, device → `loaned`
- [ ] Request reject → status `rejected`, device back to `active`

### 5.5 User Management
- [ ] Create `admin/users.php` — table of all users
- [ ] Filter by role, governorate
- [ ] Deactivate/reactivate toggle (sets `is_active`)
- [ ] Cannot deactivate own admin account

### 5.6 Admin Role Guards
- [ ] Add `requireRole('admin')` at top of every admin file (before any output)
- [ ] **Verify:** Login as admin → see dashboard stats. Approve a device → it appears in marketplace. Approve a request → device status changes to loaned. Reject a listing → donor sees rejection reason. Access admin pages as non-admin → redirect to login.

---

## Phase 6 — Communication System (Week 6)

### 6.1 Helpers
- [ ] Add `generateWhatsAppUrl($phone, $message)` to `includes/functions.php` — format phone, URL-encode message, return full `wa.me` link
- [ ] Add `formatYemeniPhone($phone)` to `includes/functions.php` — remove non-digits, prepend 967, remove leading 0

### 6.2 Post-Approval Logic
- [ ] Update `admin/action.php` — on request approve: fetch donor phone + beneficiary name + device name
- [ ] Format phone with `formatYemeniPhone()`
- [ ] Store generated WhatsApp URL and tel: link in session or pass to dashboard pages

### 6.3 Beneficiary Contact Display
- [ ] Update `dashboard-beneficiary.php` — for approved requests: show donor name + governorate
- [ ] Add "Call Donor" button (`tel:+967XXX`)
- [ ] Add "WhatsApp" button (`https://wa.me/967XXX?text=...`)
- [ ] Pre-compose message from PRD §7.5.2

### 6.4 Donor Info Display
- [ ] Update `dashboard-donor.php` — for loaned devices: show beneficiary name + governorate (no phone)
- [ ] Show device status = "Currently Loaned"
- [ ] **Verify:** Approve a request as admin → login as beneficiary → see contact buttons → click WhatsApp → opens `wa.me` with pre-filled message. Login as donor → see beneficiary name + governorate on dashboard.

---

## Phase 7 — UI Polish & Responsive (Week 7)

### 7.1 CSS Refinements
- [ ] Card hover lift (`transform: translateY(-4px)` + shadow transition)
- [ ] Glassmorphism modals completed
- [ ] Loading spinners
- [ ] Fade-in animations on page load
- [ ] Smooth transitions on all interactive elements

### 7.2 Mobile Optimization
- [ ] Touch targets ≥ 44×44px
- [ ] Scrollable modals (`overflow-y: auto`)
- [ ] Font-size ≥ 16px on inputs (prevents iOS zoom)
- [ ] Bottom padding for mobile nav
- [ ] Verify all responsive breakpoints

### 7.3 Logo & Favicon
- [ ] Create `assets/images/logo.svg` — Sanad branding, teal/white
- [ ] Create `favicon.ico`

### 7.4 RTL Audit
- [ ] Verify `dir="rtl"` and `lang="ar"` on every page
- [ ] Check text alignment, icon placement, form layout in RTL

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
- [ ] Flow 1: Register donor → list device → admin approves → appears in marketplace
- [ ] Flow 2: Register beneficiary → browse → request device → admin approves → contact buttons appear
- [ ] Flow 3: Reject flows — admin rejects listing or request → appropriate status + reason shown
- [ ] Flow 4: Edge cases — duplicate email, wrong file type, empty fields, session timeout

### 8.2 Security Testing
- [ ] Verify CSRF tokens block forged POST requests
- [ ] Verify medical docs not accessible via direct URL
- [ ] Verify role guards on every protected page
- [ ] Verify PDO prepared statements prevent SQL injection

### 8.3 Static Map Fallback
- [ ] Test device detail page without Google Maps API key — fallback image + coordinates display correctly

### 8.4 Setup Notes
- [ ] Create `setup-notes.txt` — PHP.ini requirements (upload_max_filesize, post_max_size, max_execution_time)
- [ ] Document XAMPP/WAMP setup steps
- [ ] Document database credentials configuration
- [ ] Document Google Maps API key instructions

### 8.5 Defense Presentation
- [ ] Prepare demo script covering: problem statement → platform walkthrough (all 3 roles) → technical highlights → risk mitigation → Q&A readiness
- [ ] **Verify:** Fresh XAMPP install → copy project → start Apache+MySQL → open browser → platform works without any manual setup. All 12 core pages render correctly.

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
