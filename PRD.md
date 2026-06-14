# PRD.md — Product Requirements Document

# Sanad Platform (سَنَد)

**"Technology for Human Solidarity and Healthcare"**

---

```
Document Version:    1.0.0
Status:              Final Draft
Prepared By:         Project Management & Engineering Team
Last Updated:        2025
Classification:      Academic Graduation Project — Public
```

---

## Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [Problem Statement & Background](#2-problem-statement--background)
3. [Product Vision & Value Proposition](#3-product-vision--value-proposition)
4. [Strategic Objectives & Success Metrics](#4-strategic-objectives--success-metrics)
5. [Scope Definition](#5-scope-definition)
6. [User Personas & Roles](#6-user-personas--roles)
7. [Detailed Functional Requirements](#7-detailed-functional-requirements)
8. [Technical Architecture & Stack](#8-technical-architecture--stack)
9. [Database Design](#9-database-design)
10. [Non-Functional Requirements](#10-non-functional-requirements)
11. [UI/UX Design Standards](#11-uiux-design-standards)
12. [Security Requirements](#12-security-requirements)
13. [Third-Party Integrations](#13-third-party-integrations)
14. [Project Milestones & Delivery Plan](#14-project-milestones--delivery-plan)
15. [Risk Assessment & Mitigation](#15-risk-assessment--mitigation)
16. [Graduation Defense Strengths](#16-graduation-defense-strengths)
17. [Appendix](#17-appendix)

---

## 1. Executive Summary

**Project Name:** Sanad (سَنَد)
**Project Tagline:** *Technology for Human Solidarity and Healthcare*
**Platform Type:** Responsive Web Application (Desktop & Mobile)
**Target Domain:** Community Healthcare & Sustainable Development
**Primary Market:** Yemeni Community (Initial Target Audience)
**Project Nature:** Graduation Capstone Project

Sanad is Yemen's first dedicated digital solidarity platform engineered to systematically organize the lending, exchange, and donation of used medical equipment among community members. The platform bridges a critical gap between families who own idle medical devices and patients who urgently need them but cannot afford to purchase new ones — creating a trusted, verified, and supervised ecosystem powered by clean, efficient web technology.

---

## 2. Problem Statement & Background

### 2.1 Context & Root Cause

The ongoing economic deterioration in Yemen has created a severe and widening gap in healthcare accessibility. Thousands of patients and their families face extreme financial hardship when attempting to acquire essential medical equipment, including but not limited to:

- Oxygen concentrators and cylinders
- Wheelchairs and mobility aids
- Medical beds and clinical accessories
- Vital signs monitoring devices (blood pressure monitors, pulse oximeters, glucometers)

### 2.2 The Paradox of Wasted Medical Resources

Simultaneously, thousands of Yemeni households possess these exact devices, stored unused after a patient's recovery or after the device became unnecessary. This creates a critical resource paradox:

| Side | Reality |
|------|---------|
| **Patients in Need** | Cannot afford to purchase or rent medical equipment |
| **Families with Devices** | Own idle, functional medical equipment with no efficient way to share it |
| **Current State** | No organized, safe, or trusted mechanism exists to connect both sides |

### 2.3 Gap Analysis

Currently available alternatives are either:
- **Informal & Unreliable:** Word-of-mouth sharing through personal networks, limited to small social circles
- **Commercial & Inaccessible:** Medical equipment rental shops with fees that many families cannot afford
- **Unverified & Risky:** Random social media posts with no quality control, verification, or geographic filtering

This results in **preventable suffering**, **wasted humanitarian resources**, and a **broken community solidarity loop** that Sanad is specifically designed to repair.

---

## 3. Product Vision & Value Proposition

### 3.1 Product Vision Statement

> *"To build Yemen's most trusted digital bridge between those who give and those who need — transforming idle medical assets into life-saving resources through technology, transparency, and community trust."*

### 3.2 Core Value Proposition

Sanad delivers a **Smart, Simple, and Elegant** system that guarantees:

1. **For Beneficiaries:** A reliable, dignified, and fast path to finding the exact medical device they need near them
2. **For Donors:** A structured, trusted channel to ensure their donated device reaches a genuinely deserving patient
3. **For the Community:** A sustainable circular economy model applied to the healthcare sector
4. **For Administrators:** Full oversight and governance tools to maintain integrity and prevent misuse

### 3.3 Platform Core Concept

```
[Donor lists device] → [Admin reviews listing] → [Published to Marketplace]
        ↑                                                      ↓
[Device returned]                               [Beneficiary requests device]
        ↑                                                      ↓
[Loan period ends]  ← [Direct communication] ← [Admin approves request]
```

---

## 4. Strategic Objectives & Success Metrics

### 4.1 Primary Objectives

| # | Objective | Description |
|---|-----------|-------------|
| **O-1** | **Financial Relief** | Provide free or symbolic-cost alternatives to patients who cannot afford new medical equipment |
| **O-2** | **Resource Optimization (Zero Waste)** | Activate the concept of circular economy within the community healthcare sector |
| **O-3** | **Human Verification & Trust** | Establish a structured review system for patient requests that prevents monopolization or misuse of humanitarian devices |
| **O-4** | **Geographic Accessibility** | Enable users to find medical support within their closest geographic area (by Governorate and District) |

### 4.2 Key Performance Indicators (KPIs)

| KPI | Target (Post-Launch) |
|-----|----------------------|
| Number of registered devices on the platform | ≥ 50 devices within 3 months |
| Number of successfully completed device loans | ≥ 20 successful transfers |
| Admin review turnaround time | ≤ 48 hours per request |
| User registration rate | ≥ 100 users within 3 months |
| Platform uptime | ≥ 99% availability |
| Mobile usability score | ≥ 90/100 (Lighthouse Mobile) |

---

## 5. Scope Definition

### 5.1 In-Scope Features

- ✅ User registration and authentication system (Beneficiary & Donor roles)
- ✅ Admin control panel with full content governance
- ✅ Device listing system with photos, condition ratings, and geographic pinning
- ✅ Advanced search and multi-filter marketplace
- ✅ Medical verification and loan request system
- ✅ Admin approval/rejection workflow with reason logging
- ✅ Direct WhatsApp and phone call communication trigger
- ✅ Interactive Google Maps integration for device location
- ✅ Responsive design (Desktop + Mobile)
- ✅ Automatic database setup on first run (Failsafe Auto-Setup)
- ✅ Secure file upload for medical reports and device photos

### 5.2 Out-of-Scope (V1.0)

- ❌ Native mobile application (iOS / Android)
- ❌ Internal chat or messaging system
- ❌ Online payment or financial transactions
- ❌ Multi-language support beyond Arabic
- ❌ AI-powered device recommendation engine
- ❌ SMS notification system
- ❌ Email notification system
- ❌ Physical device pickup/delivery coordination

> **Note:** Out-of-scope features may be considered for future versions (V2.0+)

---

## 6. User Personas & Roles

### 6.1 Role Overview

```
┌─────────────────────────────────────────────────────┐
│                  SANAD PLATFORM                      │
│                                                       │
│  [Beneficiary]   [Donor]   [Admin]                   │
│       ↓              ↓         ↓                      │
│   Browse &       List &    Review &                   │
│   Request        Donate    Approve                    │
└─────────────────────────────────────────────────────┘
```

---

### 6.2 Role 1: The Beneficiary (المستفيد)

**Profile:**
The patient themselves, or a family member acting on their behalf, seeking a medical device for temporary use (loan) or permanent use (donation).

**Characteristics:**
- May have low digital literacy
- Likely accessing the platform via mobile phone
- Under financial and emotional stress
- Needs simple, fast, and dignified interaction

**Permissions & Capabilities:**

| Capability | Available |
|------------|-----------|
| Browse device marketplace | ✅ |
| Use search and geographic filters | ✅ |
| View device details and photos | ✅ |
| View donor location on map | ✅ |
| Submit a loan/donation request with medical proof | ✅ |
| View request status | ✅ |
| Access donor contact after admin approval | ✅ |
| Add or manage device listings | ❌ |
| Access admin panel | ❌ |

---

### 6.3 Role 2: The Donor (المُعير / المتبرع)

**Profile:**
An individual or organization that owns a surplus medical device and wishes to contribute it to the community as either a temporary loan or a permanent donation.

**Characteristics:**
- Motivated by humanitarian values and community solidarity
- Wants assurance that the device will go to a genuinely deserving recipient
- Needs a straightforward listing process

**Permissions & Capabilities:**

| Capability | Available |
|------------|-----------|
| Register and manage personal account | ✅ |
| List a medical device with full details | ✅ |
| Upload real device photos | ✅ |
| Set device condition and loan terms | ✅ |
| Pin exact device location on Google Maps | ✅ |
| Receive contact from approved beneficiary | ✅ |
| View own device status | ✅ |
| Approve or reject individual requests | ❌ (Admin handles this) |
| Access admin panel | ❌ |

---

### 6.4 Role 3: The System Administrator (مدير النظام)

**Profile:**
The governing authority responsible for the integrity and quality control of all platform content. This may be a graduation committee, a partner health charity, or a trusted institutional body.

**Permissions & Capabilities:**

| Capability | Available |
|------------|-----------|
| Access the full Admin Control Panel | ✅ |
| Review and approve/reject new device listings | ✅ |
| Review and approve/reject beneficiary requests | ✅ |
| View attached medical reports | ✅ |
| Write rejection reasons for transparency | ✅ |
| Manage all registered users | ✅ |
| View all platform activity and statistics | ✅ |
| Trigger communication channel between parties | ✅ (automatic on approval) |

---

## 7. Detailed Functional Requirements

### 7.1 FR-01: User Account Management

**Module:** Authentication & User Management

#### 7.1.1 User Registration

| Field | Type | Validation Rules |
|-------|------|-----------------|
| Full Name | Text | Required, 3–100 characters |
| Phone Number | Text | Required, must be valid Yemeni format, used for WhatsApp/call |
| Email Address | Email | Required, unique, valid format |
| Governorate (محافظة) | Dropdown | Required, populated from predefined Yemeni governorates list |
| District (مديرية) | Dropdown | Required, dynamically loaded based on selected governorate |
| Password | Password | Required, minimum 8 characters, at least 1 number |
| Confirm Password | Password | Required, must match Password field |
| Role | System-assigned | Donor or Beneficiary (Admin role assigned manually by existing Admin) |

#### 7.1.2 Login System

- Login via Email + Password
- PHP Session-based authentication
- Session expiry after period of inactivity
- Role-based redirect after login:
  - `Beneficiary` → Marketplace homepage
  - `Donor` → Donor dashboard
  - `Admin` → Admin control panel
- Unauthorized access to admin pages must redirect to login with an error message

#### 7.1.3 Access Control Rules

```
┌─────────────────────────────────────────────────────────────┐
│ PAGE / RESOURCE         │ Beneficiary │ Donor │ Admin       │
├─────────────────────────┼─────────────┼───────┼─────────────┤
│ Marketplace (Browse)    │     ✅      │  ✅   │    ✅       │
│ Device Detail Page      │     ✅      │  ✅   │    ✅       │
│ Submit Request          │     ✅      │  ❌   │    ❌       │
│ List New Device         │     ❌      │  ✅   │    ✅       │
│ Donor Dashboard         │     ❌      │  ✅   │    ✅       │
│ Admin Control Panel     │     ❌      │  ❌   │    ✅       │
└─────────────────────────┴─────────────┴───────┴─────────────┘
```

---

### 7.2 FR-02: Marketplace & Device Catalog

**Module:** Device Discovery & Browsing

#### 7.2.1 Device Card Display

Each device is displayed as a modern visual card containing:

| Element | Description |
|---------|-------------|
| Device Photo | Primary image thumbnail (first uploaded photo) |
| Device Name | Bold, clear Arabic/English title |
| Medical Category | Icon + label (Respiratory, Mobility, etc.) |
| Operational Condition | Color-coded badge: 🟢 Excellent / 🟡 Good / 🟠 Acceptable |
| Offer Type | Label: "Permanent Donation" or "Temporary Loan" |
| Location | Governorate + District text |
| Status Badge | Available / Under Review / Loaned Out |
| "Request" Button | Visible only for authenticated Beneficiary users |

#### 7.2.2 Search & Filter Engine

The marketplace must include a powerful, intuitive filter system:

| Filter Type | Options |
|-------------|---------|
| **Text Search** | Free-text search by device name or description |
| **Geographic Filter** | Dropdown by Governorate → cascading District filter |
| **Medical Category** | Respiratory Devices / Mobility & Walking Aids / Beds & Clinical Supplies / Diagnostic & Monitoring Devices |
| **Offer Type** | All / Permanent Donation / Temporary Loan |
| **Device Condition** | All / Excellent / Good / Acceptable |

- Filters must operate without full page reload (dynamic JavaScript filtering or AJAX-based PHP calls)
- Active filters must be visually indicated to the user
- "Clear All Filters" button must be present

#### 7.2.3 Device Detail Page

Clicking a device card opens a full detail page containing:

- Full photo gallery/album (swipeable on mobile)
- Complete device description
- Operational condition details
- Offer type and loan duration (if applicable)
- Governorate + District location
- Embedded Google Map with the pinned device location
- "Request This Device" button (triggers FR-04)
- Device status indicator

---

### 7.3 FR-03: Device Listing System (Donor)

**Module:** Device Submission & Documentation

#### 7.3.1 Listing Form Fields

| Field | Type | Rules |
|-------|------|-------|
| Device Name | Text | Required |
| Medical Category | Dropdown | Required — (Respiratory / Mobility / Beds & Clinical / Diagnostic) |
| Operational Condition | Radio | Required — (Excellent / Good / Acceptable) |
| Detailed Description | Textarea | Required, min 30 characters — describe device condition, usage history, any issues |
| Offer Type | Radio | Required — (Permanent Donation / Temporary Loan) |
| Maximum Loan Duration | Dropdown | **Conditionally required** — appears only when "Temporary Loan" is selected — options: 2 Weeks / 1 Month / 3 Months / 6 Months / Negotiable |
| Device Location (Map) | Google Maps Picker | Required — donor pins location on interactive map; system captures Latitude & Longitude |
| Device Photos | File Upload (Multiple) | Required — minimum 1 photo, maximum 6 photos, formats: JPG/PNG/WEBP, max 5MB each |

#### 7.3.2 Listing Submission Workflow

```
Donor fills form → Client-side validation → Form submitted to PHP backend
→ Files uploaded securely → Record saved with status: "Pending Admin Review"
→ Donor sees confirmation message → Admin notified (panel badge updates)
→ Admin reviews → Approved: listing goes "Active" / Rejected: donor informed
```

#### 7.3.3 Donor Dashboard

The donor's personal dashboard displays:

- List of all their submitted devices with current status
- Status labels: Pending Review / Active / Under Request Review / Currently Loaned / Rejected
- Ability to view rejection reason if applicable

---

### 7.4 FR-04: Medical Verification & Request System

**Module:** Beneficiary Request & Humanitarian Verification

#### 7.4.1 Request Initiation

When an authenticated Beneficiary clicks **"Request This Device"**:

1. A modal dialog (popup) opens immediately over the device page
2. The device listing is **not yet locked** at this stage
3. The modal contains the mandatory request form (see below)

#### 7.4.2 Request Modal — Required Fields

| Field | Type | Rules |
|-------|------|-------|
| Medical Case Description | Textarea | Required, min 50 characters — Patient explains their condition and why this device is needed |
| Medical Document Upload | File Upload | Required — Official medical report, recent stamped prescription, or hospital discharge summary — formats: JPG/PNG/PDF — max 10MB |

#### 7.4.3 Post-Submission Behavior

Upon successful request submission:

- The target device listing status changes to **"Under Review"** in the database
- The device disappears from the "Available" filter results and is visually marked as unavailable to other users
- The Admin panel displays a new pending request notification
- Beneficiary sees a confirmation message: *"Your request has been submitted successfully. You will be notified once reviewed by the admin."*

> **Business Rule:** Only one active request can exist per device at any time. If rejected, the device returns to "Available" status and other beneficiaries may request it.

---

### 7.5 FR-05: Communication Workflow

**Module:** Post-Approval Contact System

#### 7.5.1 Communication Philosophy

Instead of building a complex internal messaging system (which creates unnecessary technical overhead and is ill-suited to variable local internet conditions), Sanad uses a **pragmatic direct communication model** leveraging tools the community already uses daily.

#### 7.5.2 After Admin Approval — Beneficiary Side

Upon admin approval of a request, the beneficiary's request status page displays:

| Button | Action |
|--------|--------|
| 📞 **Direct Phone Call** | Opens the device with `tel:+967XXXXXXXXX` to dial donor's number directly |
| 💬 **Contact via WhatsApp** | Opens WhatsApp with a pre-composed message using the WhatsApp API link format: `https://wa.me/967XXXXXXXXX?text=[Pre-composed message]` |

**Pre-composed WhatsApp message template:**
```
مرحباً، تم قبول طلبي عبر منصة سَنَد لاستعارة جهازك الطبي.
اسمي: [Beneficiary Name]
الجهاز المطلوب: [Device Name]
أرجو التواصل لترتيب استلام الجهاز. شكراً لكم.
```

#### 7.5.3 After Admin Approval — Donor Side

- The donor's dashboard shows the device status updated to **"Currently Loaned"**
- Donor can view the beneficiary's name and governorate (not full personal details for privacy)
- The device is fully removed from the public marketplace

---

### 7.6 FR-06: Admin Control Panel

**Module:** Governance & Content Management

#### 7.6.1 Admin Dashboard Overview Page

The admin homepage/dashboard displays summary statistics:

| Metric | Display |
|--------|---------|
| Total registered users | Count card |
| Total devices listed | Count card |
| Pending device approvals | Count card (highlighted if > 0) |
| Pending request reviews | Count card (highlighted if > 0) |
| Active (available) devices | Count card |
| Devices currently loaned | Count card |

#### 7.6.2 Section A: Device Listing Review

**Purpose:** Ensure all listed devices are genuine, appropriate, and meet platform standards before they appear publicly.

**Admin Actions per listing:**

| Action | System Effect |
|--------|---------------|
| **Approve** | Device status → "Active", becomes visible in marketplace |
| **Reject** | Admin must write a rejection reason; device status → "Rejected"; donor can see the reason on their dashboard |

**Review Interface shows:**
- Full device details as submitted
- All uploaded photos (viewable in full size)
- Donor's name and contact info
- Submission timestamp

#### 7.6.3 Section B: Beneficiary Request Review

**Purpose:** Verify the humanitarian legitimacy of each request before connecting parties.

**Admin Actions per request:**

| Action | System Effect |
|--------|---------------|
| **Approve** | • Device status → "Loaned Out" (removed from marketplace) • Donor contact info revealed to beneficiary • WhatsApp and call buttons activated on beneficiary's page |
| **Reject** | • Admin must provide rejection reason (e.g., "Medical report is unclear", "Document is outdated") • Device status → reverts to "Active" • Device reappears in marketplace for other beneficiaries • Rejected beneficiary can see reason and is encouraged to reapply with corrected documents |

**Review Interface shows:**
- Device details and photos
- Beneficiary's medical case description
- Uploaded medical document (viewable/downloadable by admin only)
- Beneficiary's name and governorate
- Submission timestamp

#### 7.6.4 Section C: User Management

- View all registered users (Beneficiaries and Donors)
- Filter by role, governorate
- Ability to deactivate a user account if misuse is detected

---

## 8. Technical Architecture & Stack

### 8.1 Architecture Overview

```
┌───────────────────────────────────────────────────────────┐
│                     CLIENT LAYER                           │
│           HTML5 + CSS3 + Vanilla JavaScript               │
│                  (Browser / Mobile)                        │
└───────────────────┬──────────────────────────────────────┘
                    │ HTTP Requests
┌───────────────────▼──────────────────────────────────────┐
│                   SERVER LAYER                             │
│              Pure PHP (Procedural / OOP)                   │
│         Session Management │ Business Logic               │
│         File Upload Handler │ Form Processing             │
└───────────────────┬──────────────────────────────────────┘
                    │ SQL Queries
┌───────────────────▼──────────────────────────────────────┐
│                  DATABASE LAYER                            │
│              MySQL (via XAMPP / WAMP)                      │
│           Auto-Setup on First Run (IF NOT EXISTS)         │
└───────────────────────────────────────────────────────────┘
                    │ External APIs
┌───────────────────▼──────────────────────────────────────┐
│               THIRD-PARTY INTEGRATIONS                     │
│    Google Maps JavaScript API │ WhatsApp URL Scheme        │
└───────────────────────────────────────────────────────────┘
```

---

### 8.2 Frontend Stack

#### 8.2.1 HTML5

- Semantic markup using appropriate elements: `<header>`, `<main>`, `<section>`, `<article>`, `<nav>`, `<footer>`, `<form>`, `<figure>`
- Accessible form design with `<label>` elements and ARIA attributes where appropriate
- Structured form validation using HTML5 native attributes (`required`, `type="email"`, `accept`, `multiple`)

#### 8.2.2 CSS3

| Feature | Implementation |
|---------|---------------|
| **CSS Custom Variables** | Define color palette, font sizes, spacing, and border radius in `:root` for system-wide consistency and easy theming |
| **CSS Flexbox** | Navigation bars, card layouts, button groups, form rows |
| **CSS Grid** | Device marketplace card grid (responsive columns: 3→2→1 based on screen width) |
| **Responsive Design** | Media queries at breakpoints: 1200px (Desktop), 768px (Tablet), 480px (Mobile) |
| **Glassmorphism Effects** | Modal/popup dialogs using `backdrop-filter: blur()`, `background: rgba()`, and `border: 1px solid rgba()` |
| **Smooth Transitions** | `transition` properties on hover states, button interactions, modal open/close animations |
| **CSS Animations** | Loading spinners, card hover lift effect (`transform: translateY`), fade-in for page elements |

**CSS Variable System Example:**
```css
:root {
  --color-primary:       #00B4D8;   /* Teal Blue — Trust, Health */
  --color-primary-dark:  #0077A8;
  --color-secondary:     #90E0EF;
  --color-accent:        #CAF0F8;
  --color-white:         #FFFFFF;
  --color-bg:            #F0F8FF;
  --color-text-dark:     #1A1A2E;
  --color-text-muted:    #6B7280;
  --color-success:       #10B981;
  --color-warning:       #F59E0B;
  --color-danger:        #EF4444;
  --radius-sm:           8px;
  --radius-md:           16px;
  --radius-lg:           24px;
  --shadow-card:         0 4px 20px rgba(0,0,0,0.08);
  --shadow-modal:        0 20px 60px rgba(0,0,0,0.2);
  --font-primary:        'Tajawal', sans-serif;   /* Arabic-English compatible */
  --transition-default:  all 0.3s ease;
}
```

#### 8.2.3 Vanilla JavaScript (ES6+)

| Feature | Implementation |
|---------|---------------|
| **Client-side Validation** | Real-time form validation before submission (field-level error display) |
| **Modal Management** | Open/close logic for request modal, photo preview modals |
| **Dynamic Filtering** | Filter marketplace cards without page reload using DOM manipulation |
| **Cascading Dropdowns** | Dynamic district list loading based on selected governorate |
| **File Preview** | Preview uploaded device photos before form submission |
| **Map Integration** | Google Maps API initialization, marker placement, coordinate capture |
| **Conditional Fields** | Show/hide loan duration field based on offer type selection |

---

### 8.3 Backend Stack

#### 8.3.1 Pure PHP (Procedural / OOP)

| Responsibility | Implementation |
|----------------|---------------|
| **Routing** | URL-based file routing (`index.php`, `marketplace.php`, `device.php?id=X`, etc.) |
| **Session Management** | `session_start()`, session-based role verification on every protected page |
| **Form Processing** | Sanitize all inputs using `htmlspecialchars()`, `strip_tags()`, `trim()` |
| **Database Interaction** | PDO with prepared statements (prevents SQL injection) |
| **File Upload Handling** | Validate MIME type, file size, extension whitelist; rename files to UUID-based names; store in protected directories |
| **Password Security** | `password_hash()` for storage, `password_verify()` for authentication |
| **Access Control** | Role-check functions called at the top of every protected page |

**PHP File Structure:**
```
/sanad/
├── index.php                   # Homepage & Marketplace
├── login.php                   # Login page
├── register.php                # Registration page
├── logout.php                  # Session destroy
├── device.php                  # Device detail page
├── add-device.php              # Donor listing form
├── request.php                 # Handle request submission (POST)
├── dashboard-donor.php         # Donor personal dashboard
├── dashboard-beneficiary.php   # Beneficiary request status
│
├── /admin/
│   ├── index.php               # Admin overview dashboard
│   ├── listings.php            # Device listing review panel
│   ├── requests.php            # Request review panel
│   ├── users.php               # User management
│   └── action.php              # Handle approve/reject POST actions
│
├── /includes/
│   ├── db.php                  # Database connection + auto-setup (CREATE IF NOT EXISTS)
│   ├── auth.php                # Authentication & role functions
│   ├── functions.php           # Shared helper functions
│   └── config.php              # Global constants (DB credentials, API keys, paths)
│
├── /assets/
│   ├── /css/
│   │   └── style.css
│   ├── /js/
│   │   ├── main.js
│   │   ├── maps.js
│   │   └── validation.js
│   └── /images/
│       └── logo.svg
│
└── /uploads/
    ├── /devices/               # Device photos (web-accessible)
    └── /medical-reports/       # Medical documents (PROTECTED — no direct web access)
```

---

## 9. Database Design

### 9.1 Failsafe Auto-Setup System

The `includes/db.php` file must implement the following on every request:

```php
// Step 1: Connect to MySQL server (no DB selected)
$pdo = new PDO("mysql:host=localhost", DB_USER, DB_PASS);

// Step 2: Create database if it doesn't exist
$pdo->exec("CREATE DATABASE IF NOT EXISTS `sanad_db` 
            CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$pdo->exec("USE `sanad_db`");

// Step 3: Create all tables using CREATE TABLE IF NOT EXISTS
// (See schema below)
```

This ensures the project runs **without any manual database import** on any XAMPP/WAMP environment during the graduation defense.

---

### 9.2 Database Schema

#### Table: `users`

```sql
CREATE TABLE IF NOT EXISTS `users` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `full_name`     VARCHAR(100)  NOT NULL,
    `phone`         VARCHAR(20)   NOT NULL UNIQUE,
    `email`         VARCHAR(150)  NOT NULL UNIQUE,
    `password_hash` VARCHAR(255)  NOT NULL,
    `role`          ENUM('beneficiary', 'donor', 'admin') NOT NULL DEFAULT 'beneficiary',
    `governorate`   VARCHAR(50)   NOT NULL,
    `district`      VARCHAR(100)  NOT NULL,
    `is_active`     TINYINT(1)    NOT NULL DEFAULT 1,
    `created_at`    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### Table: `devices`

```sql
CREATE TABLE IF NOT EXISTS `devices` (
    `id`                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `donor_id`          INT UNSIGNED NOT NULL,
    `name`              VARCHAR(150) NOT NULL,
    `category`          ENUM('respiratory','mobility','beds_clinical','diagnostic') NOT NULL,
    `condition_rating`  ENUM('excellent','good','acceptable') NOT NULL,
    `description`       TEXT NOT NULL,
    `offer_type`        ENUM('donation','loan') NOT NULL,
    `loan_duration`     VARCHAR(50)  DEFAULT NULL,
    `governorate`       VARCHAR(50)  NOT NULL,
    `district`          VARCHAR(100) NOT NULL,
    `latitude`          DECIMAL(10,8) DEFAULT NULL,
    `longitude`         DECIMAL(11,8) DEFAULT NULL,
    `status`            ENUM('pending_review','active','under_request_review','loaned','rejected') NOT NULL DEFAULT 'pending_review',
    `rejection_reason`  TEXT DEFAULT NULL,
    `created_at`        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`donor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### Table: `device_photos`

```sql
CREATE TABLE IF NOT EXISTS `device_photos` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `device_id`   INT UNSIGNED NOT NULL,
    `file_path`   VARCHAR(255) NOT NULL,
    `is_primary`  TINYINT(1)   NOT NULL DEFAULT 0,
    `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`device_id`) REFERENCES `devices`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### Table: `requests`

```sql
CREATE TABLE IF NOT EXISTS `requests` (
    `id`                    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `device_id`             INT UNSIGNED NOT NULL,
    `beneficiary_id`        INT UNSIGNED NOT NULL,
    `case_description`      TEXT NOT NULL,
    `medical_doc_path`      VARCHAR(255) NOT NULL,
    `status`                ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    `rejection_reason`      TEXT DEFAULT NULL,
    `admin_reviewed_by`     INT UNSIGNED DEFAULT NULL,
    `admin_reviewed_at`     TIMESTAMP DEFAULT NULL,
    `created_at`            TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`device_id`)         REFERENCES `devices`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`beneficiary_id`)    REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`admin_reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 9.3 Entity Relationship Diagram (ERD)

```
users (id) ─────────────────────────────────── devices (donor_id)
   │                                                   │
   │ (beneficiary_id)                       (device_id)│
   └──────────────── requests ─────────────────────────┘
                        │
                (admin_reviewed_by)
                        │
                     users (id)

devices (id) ──────────────── device_photos (device_id)
```

---

## 10. Non-Functional Requirements

### 10.1 Performance Requirements

| Requirement | Standard |
|-------------|----------|
| Page load time (3G connection) | ≤ 3 seconds |
| Image optimization | All uploads compressed, max display size optimized via CSS |
| No heavy framework overhead | Pure PHP/CSS/JS ensures minimal payload |
| Efficient database queries | Use of indexed columns (`status`, `governorate`, `donor_id`) |

### 10.2 Responsiveness Requirements

The platform must be fully functional and visually appealing across:

| Device Type | Screen Width | Expected Behavior |
|-------------|-------------|-------------------|
| Large Desktop | ≥ 1200px | 3-column device grid, full sidebar filters |
| Tablet | 768px – 1199px | 2-column grid, collapsible filter panel |
| Mobile | < 768px | 1-column card stack, bottom navigation, touch-optimized buttons |

**Mobile-specific requirements:**
- Minimum touch target size: 44×44px for all interactive elements
- Modal dialogs must be scrollable and not overflow the screen
- Map interface must be touch-friendly (pinch to zoom)
- Form inputs must not trigger unwanted zoom on iOS (font-size ≥ 16px)

### 10.3 Accessibility Requirements

- Color contrast ratio ≥ 4.5:1 for all text (WCAG AA standard)
- All form fields must have associated `<label>` elements
- Error messages must be descriptive and appear near the relevant field
- Arabic RTL layout must be properly implemented using `dir="rtl"` and `lang="ar"`

### 10.4 Browser Compatibility

| Browser | Minimum Version |
|---------|----------------|
| Google Chrome | 90+ |
| Firefox | 88+ |
| Safari (iOS) | 14+ |
| Samsung Internet | 13+ |
| Microsoft Edge | 90+ |

---

## 11. UI/UX Design Standards

### 11.1 Design Language & Philosophy

The platform's design must communicate **trust, care, and simplicity** — values that align with its humanitarian mission.

**Design Principles:**
1. **Clarity First:** Every element has a clear purpose; no decorative clutter
2. **Minimum Clicks:** Core actions (browse → request) achievable in 3 clicks or fewer
3. **Dignified Experience:** Design must not feel "charity-like" or degrading — it should feel modern and empowering
4. **Mobile-Native Feel:** The interface should feel like a native app when viewed on a smartphone

### 11.2 Color System

| Role | Color | Hex | Usage |
|------|-------|-----|-------|
| Primary | Teal Blue | `#00B4D8` | Main CTAs, links, header |
| Primary Dark | Deep Teal | `#0077A8` | Hover states, active tabs |
| Secondary | Sky Blue | `#90E0EF` | Accent elements, badges |
| Background | Ice White | `#F0F8FF` | Page background |
| Surface | White | `#FFFFFF` | Cards, modals |
| Text Dark | Navy | `#1A1A2E` | Headings, body text |
| Text Muted | Gray | `#6B7280` | Secondary text, placeholders |
| Success | Emerald | `#10B981` | Approval badges, success messages |
| Warning | Amber | `#F59E0B` | Under Review status |
| Danger | Red | `#EF4444` | Rejection, errors |

### 11.3 Typography

| Element | Font | Weight | Size |
|---------|------|--------|------|
| Page Title | Tajawal | 700 (Bold) | 2rem |
| Section Heading | Tajawal | 600 (SemiBold) | 1.5rem |
| Card Title | Tajawal | 600 | 1.1rem |
| Body Text | Tajawal | 400 (Regular) | 1rem |
| Small/Label | Tajawal | 400 | 0.85rem |
| Buttons | Tajawal | 600 | 0.95rem |

> Tajawal is a free Google Font that supports both Arabic and Latin characters with excellent readability.

### 11.4 Component Standards

**Device Card:**
```
┌──────────────────────────────┐
│  [Device Photo - 200px tall] │
│──────────────────────────────│
│  🏷️ Category Badge            │
│  Device Name (Bold)          │
│  📍 Governorate, District    │
│  ⚙️ Condition: [Badge]       │
│  🎁 Offer Type: [Badge]      │
│  [    Request Button    ]    │
└──────────────────────────────┘
```

**Request Modal:**
```
┌────────────────────────────────────────┐
│  ✕   Request Device: [Device Name]     │
│────────────────────────────────────────│
│  📋 Describe your medical case:        │
│  [___________________________________] │
│  [___________________________________] │
│                                        │
│  📎 Upload Medical Document:           │
│  [  Choose File  ]  No file chosen     │
│  (Accepted: PDF, JPG, PNG — Max 10MB)  │
│                                        │
│  [Cancel]    [Submit Request →]        │
└────────────────────────────────────────┘
```
*Modal uses Glassmorphism: `backdrop-filter: blur(12px)` with semi-transparent background*

### 11.5 Page Structure Map

```
/index.php (Homepage)
    ├── Hero Section (Mission statement + CTA buttons)
    ├── Stats Bar (Devices Available, Families Helped, Governorates)
    ├── How It Works (3-step visual guide)
    └── CTA: Browse Devices / List a Device

/marketplace.php
    ├── Search Bar (full width)
    ├── Filter Panel (Sidebar on desktop, collapsible on mobile)
    └── Device Cards Grid

/device.php?id=X
    ├── Photo Gallery
    ├── Device Details
    ├── Map Embed
    └── Request Button

/add-device.php (Donor only)
    └── Multi-section listing form with map picker

/admin/index.php
    ├── Statistics Dashboard
    ├── Pending Listings Queue
    └── Pending Requests Queue
```

---

## 12. Security Requirements

### 12.1 Authentication Security

| Threat | Mitigation |
|--------|-----------|
| Brute force login | Rate limiting consideration; CAPTCHA for V2 |
| Session hijacking | `session_regenerate_id()` after login; HttpOnly cookies |
| Unauthorized admin access | Role check at the top of every admin PHP file; redirect if not admin |
| Password exposure | `password_hash(PASSWORD_BCRYPT)` for all stored passwords |

### 12.2 Input & Data Security

| Threat | Mitigation |
|--------|-----------|
| SQL Injection | PDO prepared statements for ALL database queries — no string concatenation |
| XSS (Cross-site Scripting) | `htmlspecialchars()` on all output; `strip_tags()` on text inputs |
| CSRF Attacks | CSRF tokens on all POST forms (generate token in session, validate on submit) |
| Directory Traversal | Validate file paths; store uploads outside web root or use randomized names |

### 12.3 File Upload Security

| Check | Implementation |
|-------|---------------|
| File extension whitelist | Only allow: `.jpg`, `.jpeg`, `.png`, `.webp`, `.pdf` |
| MIME type verification | Use `finfo_file()` to verify real MIME type (not just extension) |
| File size limit | PHP: `upload_max_filesize`, enforced in code |
| Rename uploaded files | UUID v4 naming to prevent path guessing: `a3f2b1c4-...jpg` |
| Medical docs protection | Store in `/uploads/medical-reports/` with `.htaccess` blocking direct access; serve only through authenticated PHP script |

### 12.4 Medical Data Privacy

- Medical reports are **never accessible via direct URL**
- Only Admin role can view/download medical documents through authenticated PHP endpoint
- Beneficiary personal data (full phone number) is only revealed to Donor **after** Admin approval
- Donor phone number only shared with Beneficiary **after** Admin approval

---

## 13. Third-Party Integrations

### 13.1 Google Maps JavaScript API

| Feature | Implementation |
|---------|---------------|
| **Donor Map Picker** | Interactive map on `add-device.php` allowing donor to drag/drop a pin to exact device location |
| **Coordinate Capture** | JavaScript event listener captures `lat` and `lng` on pin placement; hidden form fields updated |
| **Device Location Display** | Read-only embedded map on device detail page showing device location |
| **Geocoding** | Optional: Auto-populate governorate/district from coordinates using Geocoding API |

**Map Integration Code Flow:**
```javascript
// maps.js
function initMap() {
    const map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: 15.5527, lng: 48.5164 }, // Yemen center
        zoom: 6
    });
    
    let marker;
    map.addListener('click', (event) => {
        if (marker) marker.setMap(null);
        marker = new google.maps.Marker({
            position: event.latLng,
            map: map
        });
        document.getElementById('latitude').value  = event.latLng.lat();
        document.getElementById('longitude').value = event.latLng.lng();
    });
}
```

### 13.2 WhatsApp URL Scheme Integration

**WhatsApp API Format:**
```
https://wa.me/[CountryCode+PhoneNumber]?text=[URL-Encoded-Message]
```

**Implementation:**
```php
// In PHP after admin approval:
$phone    = preg_replace('/[^0-9]/', '', $donor['phone']);
$phone    = '967' . ltrim($phone, '0'); // Yemen country code
$message  = urlencode(
    "مرحباً، تم قبول طلبي عبر منصة سَنَد لاستعارة جهازك الطبي.\n" .
    "اسمي: {$beneficiary['full_name']}\n" .
    "الجهاز المطلوب: {$device['name']}\n" .
    "أرجو التواصل لترتيب استلام الجهاز. شكراً لكم."
);
$whatsapp_url = "https://wa.me/{$phone}?text={$message}";
```

**Display Button:**
```html
<a href="<?= $whatsapp_url ?>" target="_blank" class="btn btn-whatsapp">
    💬 تواصل عبر الواتساب
</a>
<a href="tel:<?= $donor_phone ?>" class="btn btn-call">
    📞 اتصال هاتفي مباشر
</a>
```

---

## 14. Project Milestones & Delivery Plan

### 14.1 Development Phases

| Phase | Name | Key Deliverables | Duration |
|-------|------|-----------------|----------|
| **Phase 0** | Setup & Architecture | File structure, DB auto-setup script, config, base CSS variables | Week 1 |
| **Phase 1** | Authentication | Registration, login, logout, session management, role-based access | Week 1–2 |
| **Phase 2** | Marketplace & Catalog | Marketplace page, device cards, search filters, device detail page | Week 2–3 |
| **Phase 3** | Device Listing System | Donor form, map picker, photo upload, listing submission | Week 3–4 |
| **Phase 4** | Request System | Request modal, medical doc upload, status management | Week 4–5 |
| **Phase 5** | Admin Control Panel | Dashboard stats, listing review, request review, approve/reject logic | Week 5–6 |
| **Phase 6** | Communication System | WhatsApp links, phone call links, contact reveal logic | Week 6 |
| **Phase 7** | UI Polish & Responsive | Final CSS refinement, mobile optimization, animations, testing | Week 7 |
| **Phase 8** | Testing & Defense Prep | Full QA, cross-browser test, presentation preparation | Week 8 |

### 14.2 Deliverables Checklist

**Core Pages:**
- [ ] Homepage (`index.php`)
- [ ] Registration Page (`register.php`)
- [ ] Login Page (`login.php`)
- [ ] Marketplace / Device Catalog (`marketplace.php`)
- [ ] Device Detail Page (`device.php`)
- [ ] Add Device Page — Donor only (`add-device.php`)
- [ ] Donor Dashboard (`dashboard-donor.php`)
- [ ] Beneficiary Request Status (`dashboard-beneficiary.php`)
- [ ] Admin Overview Dashboard (`admin/index.php`)
- [ ] Admin Listings Review (`admin/listings.php`)
- [ ] Admin Requests Review (`admin/requests.php`)
- [ ] Admin User Management (`admin/users.php`)

**Backend Modules:**
- [ ] Database auto-setup (`includes/db.php`)
- [ ] Authentication functions (`includes/auth.php`)
- [ ] Helper functions (`includes/functions.php`)
- [ ] File upload handler (secure)
- [ ] Admin action handler (`admin/action.php`)

**Frontend Assets:**
- [ ] Complete CSS stylesheet with variables system
- [ ] JavaScript validation module
- [ ] Google Maps integration module
- [ ] Responsive breakpoints implemented
- [ ] Glassmorphism modal styles
- [ ] Platform logo and favicon

---

## 15. Risk Assessment & Mitigation

| Risk | Likelihood | Impact | Mitigation Strategy |
|------|-----------|--------|---------------------|
| Google Maps API key not available during defense | Medium | High | Prepare a static map fallback image; show coordinates as text as backup |
| Database import fails on defense machine | Low | Critical | Failsafe Auto-Setup system (CREATE IF NOT EXISTS) eliminates this risk entirely |
| Slow internet during defense | Medium | Medium | All core functionality works locally on XAMPP; WhatsApp demo can be shown on phone |
| File upload size restrictions | Low | Medium | Document PHP.ini configuration requirements; provide a `setup-notes.txt` |
| Scope creep (adding unnecessary features) | Medium | Medium | Strictly adhere to V1.0 scope defined in this PRD |
| Medical report privacy breach | Low | High | Serve all medical docs via authenticated PHP script, never direct URL |
| Mobile layout issues during defense demo | Low | High | Thorough mobile testing; use browser DevTools mobile simulation as backup |

---

## 16. Graduation Defense Strengths

### 16.1 Real-World Community Impact (Local Relevance)

This project does not present a theoretical or generic idea. It addresses **a real, observable crisis** that committee members, doctors, and families experience daily in the Yemeni context. The platform directly answers the question: *"How does this benefit society?"* — with a concrete, measurable answer.

**Talking Point:** *"Every committee member either knows a family that has an unused oxygen concentrator at home, or knows a patient who desperately needs one. Sanad is the bridge between them."*

### 16.2 Engineering Maturity & Code Discipline

| Decision | Engineering Justification |
|----------|--------------------------|
| Pure PHP (no Laravel/Symfony) | Demonstrates mastery of fundamental backend logic: sessions, PDO, file handling, without framework abstraction hiding the concepts |
| Vanilla JavaScript (no React/Vue) | Proves understanding of DOM manipulation, event handling, async behavior without library dependencies |
| CSS without Bootstrap/Tailwind | Custom responsive design proves real understanding of layout systems (Grid, Flexbox) |
| PDO Prepared Statements | Shows awareness of security best practices at the code level |
| Auto-Setup Database | Demonstrates systems thinking — anticipating environment differences and solving them proactively |

### 16.3 Data Governance & Responsible Design

The mandatory Admin approval layer before any connection is made between parties demonstrates:

- **Ethical Engineering:** Awareness that a medical platform carries moral responsibility
- **Privacy by Design:** Medical data is protected, not casually exposed
- **Abuse Prevention:** The verification step prevents misuse of humanitarian resources
- **Accountability:** Every approval/rejection is logged with admin ID and timestamp

### 16.4 Smart Architecture Decisions

| Decision | Smart Engineering Principle Applied |
|----------|--------------------------------------|
| WhatsApp for communication | Use the best existing tool for the job; avoid over-engineering |
| No internal chat system | YAGNI (You Aren't Gonna Need It) — simplicity serves users better |
| Geographic filtering | Solves the core UX problem of proximity — a medical device 500km away is useless |
| Device locking on request | Prevents race conditions: two people cannot request the same device simultaneously |

---

## 17. Appendix

### 17.1 Yemeni Governorates Reference List

For dropdown population in registration and device forms:

```
صنعاء (Sana'a) | عدن (Aden) | تعز (Taiz) | الحديدة (Al-Hudaydah)
حضرموت (Hadhramaut) | إب (Ibb) | ذمار (Dhamar) | البيضاء (Al-Bayda)
المحويت (Al-Mahwit) | ريمة (Raymah) | مأرب (Marib) | الجوف (Al-Jawf)
شبوة (Shabwah) | أبين (Abyan) | لحج (Lahj) | الضالع (Ad-Dali')
المهرة (Al-Mahrah) | سقطرى (Socotra) | صعدة (Sa'dah) | عمران (Amran)
```

### 17.2 Medical Categories Definition

| Category Key | Arabic Name | Example Devices |
|-------------|-------------|-----------------|
| `respiratory` | أجهزة تنفسية | Oxygen concentrators, cylinders, nebulizers, CPAP machines |
| `mobility` | أجهزة حركة ومشي | Wheelchairs, crutches, walkers, prosthetic supports |
| `beds_clinical` | أسرة ومستلزمات سريرية | Hospital beds, pressure mattresses, IV stands, patient lifts |
| `diagnostic` | أجهزة فحص ومراقبة | Blood pressure monitors, pulse oximeters, glucometers, thermometers |

### 17.3 Device Status State Machine

```
                   ┌─────────────┐
       Donor       │  pending_   │  Admin Review
       Submits ──► │  review     │ ──────────────► REJECTED
                   └─────────────┘                    ↑
                          │                           │
                   Admin Approves                     │
                          │                           │
                          ▼                           │
                   ┌─────────────┐             ┌──────────────┐
                   │   active    │◄────────────│  under       │
                   │ (Available) │ Request      │  request     │
                   └─────────────┘ Rejected     │  review      │
                          │                    └──────────────┘
                   Beneficiary                        ▲
                   Requests ──────────────────────────┘
                          │
                   Admin Approves Request
                          │
                          ▼
                   ┌─────────────┐
                   │   loaned    │
                   │ (Completed) │
                   └─────────────┘
```

### 17.4 Glossary

| Term | Definition |
|------|-----------|
| **Beneficiary (مستفيد)** | A patient or family member seeking to borrow or receive a medical device |
| **Donor (متبرع/معير)** | An individual or organization listing a surplus medical device for sharing |
| **Admin (مشرف)** | Platform administrator responsible for content governance and request approval |
| **Device Listing** | A submitted record of a medical device available for donation or loan |
| **Medical Request** | A beneficiary's formal application for a listed device, with attached medical proof |
| **Circular Economy** | An economic system aimed at eliminating waste by reusing and cycling resources |
| **Glassmorphism** | A UI design trend using frosted glass effects via CSS `backdrop-filter` |
| **PDO** | PHP Data Objects — a secure database access layer supporting prepared statements |
| **XAMPP/WAMP** | Local server environments for running PHP+MySQL on a personal computer |
| **Auto-Setup** | The platform's ability to create its own database and tables on first run, requiring no manual SQL import |

---

```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                    END OF DOCUMENT — PRD v1.0.0
                    Project: Sanad Platform (سَنَد)
              "Technology for Human Solidarity and Healthcare"
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```