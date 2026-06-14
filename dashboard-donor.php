<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireRole('donor');
$currentUser = getCurrentUser();
$csrf = generateCSRFToken();
?><!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>لوحة التحكم — المتبرع | سند</title>
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
  <div class="max-w-4xl mx-auto p-6 fade-in">
    <div class="flex items-center justify-between mb-8">
      <h1 class="text-2xl font-bold">مرحباً، <?= htmlspecialchars($currentUser['full_name']) ?></h1>
      <a href="logout.php" class="text-red-500 hover:text-red-700 transition text-sm">تسجيل الخروج</a>
    </div>

    <div class="glass rounded-2xl p-8 text-center">
      <p class="text-text-muted text-lg mb-6">سيتم إضافة قائمة أجهزتك هنا قريباً</p>
      <div class="flex gap-4 justify-center">
        <a href="add-device.php" class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl">
          إضافة جهاز جديد
        </a>
        <a href="marketplace.php" class="bg-white hover:bg-accent text-primary border-2 border-primary px-6 py-3 rounded-xl font-semibold transition-all duration-300">
          السوق
        </a>
      </div>
    </div>
  </div>
</body>
</html>
