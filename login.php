<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$error   = '';
$success = '';

if (isset($_SESSION['register_success'])) {
    $success = $_SESSION['register_success'];
    unset($_SESSION['register_success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'رمز التحقق غير صالح. يرجى المحاولة مرة أخرى';
    } else {
        $result = loginUser($_POST['email'] ?? '', $_POST['password'] ?? '');
        if ($result['success']) {
            $redirect = match ($result['role']) {
                'admin'        => 'admin/index.php',
                'donor'        => 'dashboard-donor.php',
                'beneficiary'  => 'marketplace.php',
                default        => 'marketplace.php',
            };
            redirect($redirect);
        }
        $error = $result['message'];
    }
}

$csrf = generateCSRFToken();
?><!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تسجيل الدخول | سند</title>
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
<body class="font-tajawal bg-bg text-text-dark min-h-screen">
  <div class="min-h-screen flex items-center justify-center p-4">
    <div class="glass w-full max-w-md rounded-2xl p-8 fade-in">
      <div class="text-center mb-8">
        <a href="index.php"><img src="assets/images/logo.svg" alt="سند" class="h-12 mx-auto"></a>
        <p class="text-text-muted mt-1">تسجيل الدخول إلى حسابك</p>
      </div>

      <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <form method="POST" class="space-y-5">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

        <div>
          <label for="email" class="block text-sm font-medium text-text-dark mb-1">البريد الإلكتروني</label>
          <input type="email" name="email" id="email" required
                 class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition" 
                 placeholder="your@email.com">
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-text-dark mb-1">كلمة المرور</label>
          <input type="password" name="password" id="password" required
                 class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition"
                 placeholder="••••••••">
        </div>

        <div class="flex items-center justify-between">
          <label class="flex items-center gap-2 text-sm text-text-muted cursor-pointer">
            <input type="checkbox" name="remember" class="rounded border-gray-300 text-primary focus:ring-primary">
            تذكرني
          </label>
          <a href="#" class="text-sm text-primary hover:text-primary-dark transition">نسيت كلمة المرور؟</a>
        </div>

        <button type="submit" class="bg-primary hover:bg-primary-dark text-white w-full py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl">
          تسجيل الدخول
        </button>
      </form>

      <p class="text-center text-text-muted text-sm mt-6">
        ليس لديك حساب؟
        <a href="register.php" class="text-primary hover:text-primary-dark font-semibold">إنشاء حساب جديد</a>
      </p>
    </div>
  </div>
</body>
</html>
