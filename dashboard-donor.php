<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireRole('donor');
$currentUser = getCurrentUser();
$csrf = generateCSRFToken();

$stmt = $pdo->prepare("SELECT * FROM devices WHERE donor_id = ? ORDER BY created_at DESC");
$stmt->execute([$currentUser['id']]);
$devices = $stmt->fetchAll();

$stmtBeneficiary = $pdo->prepare("SELECT u.full_name, u.governorate FROM requests r JOIN users u ON r.beneficiary_id = u.id WHERE r.device_id = ? AND r.status = 'approved' LIMIT 1");

$categoryLabels = [
    'respiratory' => 'جهاز تنفسي',
    'mobility' => 'جهاز حركي',
    'beds_clinical' => 'أسرة وسريرية',
    'diagnostic' => 'تشخيصي',
];

$statusLabels = [
    'pending_review' => 'قيد المراجعة',
    'active' => 'نشط',
    'under_request_review' => 'قيد طلب الإعارة',
    'loaned' => 'معار',
    'rejected' => 'مرفوض',
];

$statusClasses = [
    'pending_review' => 'bg-amber-100 text-amber-800',
    'active' => 'bg-green-100 text-green-800',
    'under_request_review' => 'bg-blue-100 text-blue-800',
    'loaned' => 'bg-purple-100 text-purple-800',
    'rejected' => 'bg-red-100 text-red-800',
];
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

<nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
  <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
    <a href="index.php" class="text-2xl font-bold text-primary">سند</a>
    <div class="flex gap-4 items-center">
      <a href="marketplace.php" class="text-text-muted hover:text-primary transition">السوق</a>
      <a href="add-device.php" class="text-text-muted hover:text-primary transition">إضافة جهاز</a>
      <a href="logout.php" class="text-red-500 hover:text-red-700 transition">تسجيل الخروج</a>
    </div>
  </div>
</nav>

<div class="max-w-5xl mx-auto p-6 fade-in">
  <div class="flex items-center justify-between mb-8">
    <h1 class="text-2xl font-bold">مرحباً، <?= htmlspecialchars($currentUser['full_name']) ?></h1>
  </div>

  <div class="mb-8">
    <a href="add-device.php" class="inline-block bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl">إضافة جهاز جديد</a>
  </div>

  <div class="glass rounded-2xl p-6 overflow-x-auto">
    <?php if (empty($devices)): ?>
      <div class="text-center py-12">
        <p class="text-text-muted text-lg">لا توجد أجهزة مضافة بعد</p>
        <a href="add-device.php" class="inline-block mt-4 text-primary hover:text-primary-dark font-semibold">أضف جهازك الأول الآن</a>
      </div>
    <?php else: ?>
      <table class="w-full text-right">
        <thead>
          <tr class="border-b border-gray-200">
            <th class="pb-3 font-semibold text-text-muted">اسم الجهاز</th>
            <th class="pb-3 font-semibold text-text-muted">التصنيف</th>
            <th class="pb-3 font-semibold text-text-muted">الحالة</th>
            <th class="pb-3 font-semibold text-text-muted">تاريخ الإضافة</th>
            <th class="pb-3 font-semibold text-text-muted">سبب الرفض</th>
            <th class="pb-3 font-semibold text-text-muted">إجراءات</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($devices as $device): ?>
            <tr class="border-b border-gray-100 hover:bg-white/50 transition">
              <td class="py-3 font-medium"><?= htmlspecialchars($device['name']) ?></td>
              <td class="py-3"><?= $categoryLabels[$device['category']] ?? $device['category'] ?></td>
              <td class="py-3">
                <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $statusClasses[$device['status']] ?>">
                  <?= $statusLabels[$device['status']] ?? $device['status'] ?>
                </span>
                <?php if ($device['status'] === 'loaned'): ?>
                  <?php $stmtBeneficiary->execute([$device['id']]); $info = $stmtBeneficiary->fetch(); if ($info): ?>
                    <div class="mt-2 space-y-1 text-xs text-text-muted">
                      <div>المستفيد: <?= htmlspecialchars($info['full_name']) ?></div>
                      <div>المحافظة: <?= htmlspecialchars($info['governorate']) ?></div>
                    </div>
                  <?php endif; ?>
                <?php endif; ?>
              </td>
              <td class="py-3 text-text-muted text-sm"><?= date('Y-m-d', strtotime($device['created_at'])) ?></td>
              <td class="py-3 text-sm">
                <?= ($device['status'] === 'rejected' && $device['rejection_reason']) ? htmlspecialchars($device['rejection_reason']) : '—' ?>
              </td>
              <td class="py-3">
                <?php if ($device['status'] === 'active'): ?>
                  <a href="device.php?id=<?= $device['id'] ?>" class="text-primary hover:text-primary-dark font-semibold text-sm">عرض في السوق</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
