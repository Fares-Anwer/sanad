<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
$csrf = generateCSRFToken();
?><!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>سند — Sanad</title>
  <link rel="icon" type="image/svg+xml" href="assets/images/favicon.svg">
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
<body class="font-tajawal bg-bg text-text-dark">
  <section class="min-h-screen flex items-center justify-center relative overflow-hidden">
    <nav class="absolute top-0 left-0 right-0 p-4 flex justify-between items-center max-w-6xl mx-auto w-full">
      <a href="index.php"><img src="assets/images/logo.svg" alt="سند" class="h-10"></a>
      <div class="flex gap-4 items-center">
        <a href="marketplace.php" class="text-text-muted hover:text-primary transition">السوق</a>
        <?php if (isLoggedIn()): ?>
          <a href="logout.php" class="text-text-muted hover:text-primary transition">خروج</a>
        <?php else: ?>
          <a href="login.php" class="text-text-muted hover:text-primary transition">دخول</a>
          <a href="register.php" class="bg-primary text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-primary-dark transition">حساب جديد</a>
        <?php endif; ?>
      </div>
    </nav>
    <div class="text-center fade-in">
      <h1 class="text-5xl font-bold text-primary mb-4">سند</h1>
      <p class="text-xl text-text-muted mb-8">منصة التكافل الطبي — Medical Equipment Solidarity</p>
      <div class="flex gap-4 justify-center">
        <a href="login.php" class="bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl">دخول</a>
        <a href="register.php" class="bg-white hover:bg-accent text-primary border-2 border-primary px-8 py-3 rounded-xl font-semibold transition-all duration-300">حساب جديد</a>
      </div>
    </div>
  </section>

  <section class="bg-primary py-8">
    <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-6 text-center text-white">
      <div class="fade-in">
        <div class="text-3xl font-bold">١٢٠+</div>
        <div class="text-lg opacity-90">جهاز متاح</div>
      </div>
      <div class="fade-in">
        <div class="text-3xl font-bold">٤٥+</div>
        <div class="text-lg opacity-90">أسرة مستفيدة</div>
      </div>
      <div class="fade-in">
        <div class="text-3xl font-bold">٢١</div>
        <div class="text-lg opacity-90">محافظة يمنية</div>
      </div>
    </div>
  </section>

  <section class="py-20 max-w-6xl mx-auto px-4">
    <h2 class="text-3xl font-bold text-center mb-12">كيف تعمل المنصة</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <div class="glass rounded-2xl p-6 text-center fade-in">
        <div class="text-4xl font-bold text-primary mb-4">١</div>
        <h3 class="text-xl font-semibold mb-2">المتبرعون يسجلون الأجهزة</h3>
        <p class="text-text-muted">يقوم المتبرعون بتسجيل الأجهزة الطبية المتاحة لديهم مع صور وتفاصيل دقيقة</p>
      </div>
      <div class="glass rounded-2xl p-6 text-center fade-in">
        <div class="text-4xl font-bold text-primary mb-4">٢</div>
        <h3 class="text-xl font-semibold mb-2">فريق سند يراجع ويثبت</h3>
        <p class="text-text-muted">فريق المنصة يراجع كل إعلان ويتأكد من صحته قبل النشر</p>
      </div>
      <div class="glass rounded-2xl p-6 text-center fade-in">
        <div class="text-4xl font-bold text-primary mb-4">٣</div>
        <h3 class="text-xl font-semibold mb-2">المستفيدون يطلبون الجهاز</h3>
        <p class="text-text-muted">يستعرض المستفيدون الأجهزة المتاحة ويقدمون طلباتهم مباشرة</p>
      </div>
    </div>
  </section>

  <section class="bg-accent py-16 text-center">
    <h2 class="text-2xl font-bold mb-4">انضم إلينا الآن</h2>
    <p class="text-text-muted mb-8">كن جزءاً من التكافل الطبي في اليمن</p>
    <div class="flex gap-4 justify-center flex-wrap">
      <a href="register.php" class="bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg">إنشاء حساب جديد</a>
      <a href="marketplace.php" class="bg-white hover:bg-accent text-primary border-2 border-primary px-8 py-3 rounded-xl font-semibold transition-all duration-300">تصفح الأجهزة</a>
    </div>
  </section>

  <footer class="bg-text-dark text-white text-center py-6 text-sm">
    <p>© 2026 سند — منصة التكافل الطبي. جميع الحقوق محفوظة.</p>
  </footer>
</body>
</html>
