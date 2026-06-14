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
      <div class="text-4xl mb-2">👥</div>
      <div class="text-3xl font-bold text-text-dark"><?= $totalUsers ?></div>
      <div class="text-text-muted text-sm mt-1">إجمالي المستخدمين</div>
    </div>

    <div class="glass rounded-2xl p-6 text-center card-hover">
      <div class="text-4xl mb-2">📦</div>
      <div class="text-3xl font-bold text-text-dark"><?= $totalDevices ?></div>
      <div class="text-text-muted text-sm mt-1">إجمالي الأجهزة</div>
    </div>

    <div class="glass rounded-2xl p-6 text-center card-hover border-2 border-amber-400">
      <div class="text-4xl mb-2">⏳</div>
      <div class="text-3xl font-bold text-amber-600"><?= $pendingDevices ?></div>
      <div class="text-text-muted text-sm mt-1">في انتظار مراجعة الأجهزة</div>
    </div>

    <div class="glass rounded-2xl p-6 text-center card-hover border-2 border-amber-400">
      <div class="text-4xl mb-2">📋</div>
      <div class="text-3xl font-bold text-amber-600"><?= $pendingRequests ?></div>
      <div class="text-text-muted text-sm mt-1">في انتظار مراجعة الطلبات</div>
    </div>

    <div class="glass rounded-2xl p-6 text-center card-hover">
      <div class="text-4xl mb-2">✅</div>
      <div class="text-3xl font-bold text-green-600"><?= $activeDevices ?></div>
      <div class="text-text-muted text-sm mt-1">الأجهزة النشطة</div>
    </div>

    <div class="glass rounded-2xl p-6 text-center card-hover">
      <div class="text-4xl mb-2">🔄</div>
      <div class="text-3xl font-bold text-purple-600"><?= $loanedDevices ?></div>
      <div class="text-text-muted text-sm mt-1">الأجهزة المعارة</div>
    </div>
  </div>

  <h2 class="text-xl font-bold mb-4">روابط سريعة</h2>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <a href="listings.php" class="glass rounded-2xl p-5 text-center card-hover hover:bg-white/60 transition block">
      <div class="text-3xl mb-2">📱</div>
      <div class="font-semibold text-text-dark">مراجعة الأجهزة</div>
    </a>
    <a href="requests.php" class="glass rounded-2xl p-5 text-center card-hover hover:bg-white/60 transition block">
      <div class="text-3xl mb-2">📝</div>
      <div class="font-semibold text-text-dark">مراجعة الطلبات</div>
    </a>
    <a href="users.php" class="glass rounded-2xl p-5 text-center card-hover hover:bg-white/60 transition block">
      <div class="text-3xl mb-2">👤</div>
      <div class="font-semibold text-text-dark">إدارة المستخدمين</div>
    </a>
    <a href="../marketplace.php" class="glass rounded-2xl p-5 text-center card-hover hover:bg-white/60 transition block">
      <div class="text-3xl mb-2">🏪</div>
      <div class="font-semibold text-text-dark">العودة إلى السوق</div>
    </a>
  </div>
</div>

</body>
</html>
