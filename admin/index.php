<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin', '../login.php?error=unauthorized');
$currentUser = getCurrentUser();
$csrf = generateCSRFToken();

$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$totalUsers = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM devices");
$totalDevices = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM devices WHERE status = 'pending_review'");
$pendingDevices = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM requests WHERE status = 'pending'");
$pendingRequests = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM devices WHERE status = 'active'");
$activeDevices = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM devices WHERE status = 'loaned'");
$loanedDevices = $stmt->fetchColumn();
?><!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>لوحة التحكم — الإدارة | سند</title>
  <link rel="icon" type="image/svg+xml" href="../assets/images/favicon.svg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#00B4D8',
            'primary-dark': '#0077A8',
            secondary: '#90E0EF',
            accent: '#CAF0F8',
            bg: '#F0F8FF',
            'text-dark': '#1A1A2E',
            'text-muted': '#6B7280',
          },
          fontFamily: {
            tajawal: ['Tajawal', 'sans-serif'],
          },
        }
      }
    }
  </script>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="font-tajawal bg-bg text-text-dark min-h-screen">

<nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
  <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
    <a href="../index.php"><img src="../assets/images/logo.svg" alt="سند" class="h-10"></a>
    <div class="flex gap-4 items-center">
      <a href="../marketplace.php" class="text-text-muted hover:text-primary transition">السوق</a>
      <a href="listings.php" class="text-text-muted hover:text-primary transition">الأجهزة</a>
      <a href="requests.php" class="text-text-muted hover:text-primary transition">الطلبات</a>
      <a href="users.php" class="text-text-muted hover:text-primary transition">المستخدمين</a>
      <a href="../logout.php" class="text-red-500 hover:text-red-700 transition">تسجيل الخروج</a>
    </div>
  </div>
</nav>

<div class="max-w-5xl mx-auto p-6 fade-in">
  <div class="flex items-center justify-between mb-8">
    <h1 class="text-2xl font-bold">مرحباً، <?= htmlspecialchars($currentUser['full_name']) ?></h1>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <div class="glass rounded-2xl p-6 text-center card-hover">
      <svg class="w-10 h-10 mx-auto text-primary mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
  </svg>
      <div class="text-3xl font-bold text-text-dark"><?= $totalUsers ?></div>
      <div class="text-text-muted text-sm mt-1">إجمالي المستخدمين</div>
    </div>

    <div class="glass rounded-2xl p-6 text-center card-hover">
      <svg class="w-10 h-10 mx-auto text-primary mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
  </svg>
      <div class="text-3xl font-bold text-text-dark"><?= $totalDevices ?></div>
      <div class="text-text-muted text-sm mt-1">إجمالي الأجهزة</div>
    </div>

    <div class="glass rounded-2xl p-6 text-center card-hover border-2 border-amber-400">
      <svg class="w-10 h-10 mx-auto text-amber-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
  </svg>
      <div class="text-3xl font-bold text-amber-600"><?= $pendingDevices ?></div>
      <div class="text-text-muted text-sm mt-1">في انتظار مراجعة الأجهزة</div>
    </div>

    <div class="glass rounded-2xl p-6 text-center card-hover border-2 border-amber-400">
      <svg class="w-10 h-10 mx-auto text-amber-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
  </svg>
      <div class="text-3xl font-bold text-amber-600"><?= $pendingRequests ?></div>
      <div class="text-text-muted text-sm mt-1">في انتظار مراجعة الطلبات</div>
    </div>

    <div class="glass rounded-2xl p-6 text-center card-hover">
      <svg class="w-10 h-10 mx-auto text-green-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
  </svg>
      <div class="text-3xl font-bold text-green-600"><?= $activeDevices ?></div>
      <div class="text-text-muted text-sm mt-1">الأجهزة النشطة</div>
    </div>

    <div class="glass rounded-2xl p-6 text-center card-hover">
      <svg class="w-10 h-10 mx-auto text-purple-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
  </svg>
      <div class="text-3xl font-bold text-purple-600"><?= $loanedDevices ?></div>
      <div class="text-text-muted text-sm mt-1">الأجهزة المعارة</div>
    </div>
  </div>

  <h2 class="text-xl font-bold mb-4">روابط سريعة</h2>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <a href="listings.php" class="glass rounded-2xl p-5 text-center card-hover hover:bg-white/60 transition block">
      <svg class="w-8 h-8 mx-auto text-primary mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
      </svg>
      <div class="font-semibold text-text-dark">مراجعة الأجهزة</div>
    </a>
    <a href="requests.php" class="glass rounded-2xl p-5 text-center card-hover hover:bg-white/60 transition block">
      <svg class="w-8 h-8 mx-auto text-primary mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      <div class="font-semibold text-text-dark">مراجعة الطلبات</div>
    </a>
    <a href="users.php" class="glass rounded-2xl p-5 text-center card-hover hover:bg-white/60 transition block">
      <svg class="w-8 h-8 mx-auto text-primary mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
      </svg>
      <div class="font-semibold text-text-dark">إدارة المستخدمين</div>
    </a>
    <a href="../marketplace.php" class="glass rounded-2xl p-5 text-center card-hover hover:bg-white/60 transition block">
      <svg class="w-8 h-8 mx-auto text-primary mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
      </svg>
      <div class="font-semibold text-text-dark">العودة إلى السوق</div>
    </a>
  </div>
</div>

</body>
</html>
