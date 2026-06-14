<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
$csrf = generateCSRFToken();
?><!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>سند — Sanad</title>
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
  <div class="min-h-screen flex items-center justify-center">
    <div class="text-center fade-in">
      <h1 class="text-5xl font-bold text-primary mb-4">سند</h1>
      <p class="text-xl text-text-muted mb-8">منصة التكافل الطبي — Medical Equipment Solidarity</p>
      <div class="flex gap-4 justify-center">
        <a href="login.php" class="bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl">دخول</a>
        <a href="register.php" class="bg-white hover:bg-accent text-primary border-2 border-primary px-8 py-3 rounded-xl font-semibold transition-all duration-300">حساب جديد</a>
      </div>
    </div>
  </div>
</body>
</html>
