<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
$isBeneficiary = isLoggedIn() && getCurrentUser()['role'] === 'beneficiary';
$csrf = generateCSRFToken();
?><!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>السوق | سند</title>
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
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="font-tajawal bg-bg text-text-dark min-h-screen">
  <div class="max-w-6xl mx-auto p-6 fade-in">
    <div class="flex items-center justify-between mb-8">
      <h1 class="text-2xl font-bold">السوق — الأجهزة الطبية</h1>
      <div>
        <?php if (isLoggedIn()): ?>
          <a href="logout.php" class="text-red-500 hover:text-red-700 transition text-sm">تسجيل الخروج</a>
        <?php else: ?>
          <a href="login.php" class="bg-primary hover:bg-primary-dark text-white px-5 py-2 rounded-xl font-semibold transition text-sm">دخول</a>
        <?php endif; ?>
      </div>
    </div>

    <div class="glass rounded-2xl p-8 text-center">
      <p class="text-text-muted text-lg">قائمة الأجهزة — قيد الإنشاء</p>
      <?php if ($isBeneficiary): ?>
        <p class="text-text-muted text-sm mt-2">يمكنك تقديم طلب استعارة عند توفر الأجهزة</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
