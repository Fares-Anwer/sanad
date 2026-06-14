<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin', '../login.php?error=unauthorized');
$currentUser = getCurrentUser();
$csrf = generateCSRFToken();

$stmt = $pdo->prepare("SELECT d.*, u.full_name AS donor_name FROM devices d JOIN users u ON d.donor_id = u.id ORDER BY FIELD(d.status, 'pending_review', 'active', 'under_request_review', 'loaned', 'rejected'), d.created_at DESC");
$stmt->execute();
$allDevices = $stmt->fetchAll();

$pendingDevices = [];
$otherDevices = [];
foreach ($allDevices as $device) {
    if ($device['status'] === 'pending_review') {
        $pendingDevices[] = $device;
    } else {
        $otherDevices[] = $device;
    }
}

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
    'loaned' => 'معار حالياً',
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
  <title>مراجعة الأجهزة | سند</title>
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
      <a href="index.php" class="text-text-muted hover:text-primary transition">لوحة التحكم</a>
      <a href="listings.php" class="text-primary font-semibold transition">مراجعة الأجهزة</a>
      <a href="requests.php" class="text-text-muted hover:text-primary transition">الطلبات</a>
      <a href="users.php" class="text-text-muted hover:text-primary transition">المستخدمين</a>
      <a href="../marketplace.php" class="text-text-muted hover:text-primary transition">السوق</a>
      <a href="../logout.php" class="text-red-500 hover:text-red-700 transition">تسجيل الخروج</a>
    </div>
  </div>
</nav>

<div class="max-w-6xl mx-auto p-6 fade-in">

  <div class="flex items-center justify-between mb-8">
    <h1 class="text-2xl font-bold">مراجعة الأجهزة</h1>
  </div>

  <div class="glass rounded-2xl p-6 overflow-x-auto mb-8">
    <h2 class="text-lg font-semibold mb-4">في انتظار المراجعة</h2>
    <?php if (empty($pendingDevices)): ?>
      <div class="text-center py-12">
        <p class="text-text-muted text-lg">لا توجد أجهزة في انتظار المراجعة</p>
      </div>
    <?php else: ?>
      <table class="w-full text-right">
        <thead>
          <tr class="border-b border-gray-200">
            <th class="pb-3 font-semibold text-text-muted">اسم الجهاز</th>
            <th class="pb-3 font-semibold text-text-muted">المتبرع</th>
            <th class="pb-3 font-semibold text-text-muted">التصنيف</th>
            <th class="pb-3 font-semibold text-text-muted">تاريخ الإضافة</th>
            <th class="pb-3 font-semibold text-text-muted">إجراءات</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pendingDevices as $device): ?>
            <tr class="border-b border-gray-100 hover:bg-white/50 transition">
              <td class="py-3 font-medium"><?= htmlspecialchars($device['name']) ?></td>
              <td class="py-3"><?= htmlspecialchars($device['donor_name']) ?></td>
              <td class="py-3"><?= $categoryLabels[$device['category']] ?? $device['category'] ?></td>
              <td class="py-3 text-text-muted text-sm"><?= date('Y-m-d', strtotime($device['created_at'])) ?></td>
              <td class="py-3">
                <div class="flex gap-2 items-center">
                  <form method="POST" action="action.php">
                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                    <input type="hidden" name="action" value="approve_device">
                    <input type="hidden" name="device_id" value="<?= $device['id'] ?>">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition min-h-[44px]">قبول</button>
                  </form>
                  <form method="POST" action="action.php" onsubmit="return confirm('تأكيد الرفض؟')">
                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                    <input type="hidden" name="action" value="reject_device">
                    <input type="hidden" name="device_id" value="<?= $device['id'] ?>">
                    <input type="text" name="rejection_reason" required placeholder="سبب الرفض" class="border rounded px-2 py-1 text-sm">
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition min-h-[44px]">رفض</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <div class="glass rounded-2xl p-6 overflow-x-auto">
    <h2 class="text-lg font-semibold mb-4">الأجهزة الأخرى</h2>
    <?php if (empty($otherDevices)): ?>
      <div class="text-center py-12">
        <p class="text-text-muted text-lg">لا توجد أجهزة أخرى</p>
      </div>
    <?php else: ?>
      <table class="w-full text-right">
        <thead>
          <tr class="border-b border-gray-200">
            <th class="pb-3 font-semibold text-text-muted">اسم الجهاز</th>
            <th class="pb-3 font-semibold text-text-muted">المتبرع</th>
            <th class="pb-3 font-semibold text-text-muted">التصنيف</th>
            <th class="pb-3 font-semibold text-text-muted">الحالة</th>
            <th class="pb-3 font-semibold text-text-muted">تاريخ الإضافة</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($otherDevices as $device): ?>
            <tr class="border-b border-gray-100 hover:bg-white/50 transition">
              <td class="py-3 font-medium"><?= htmlspecialchars($device['name']) ?></td>
              <td class="py-3"><?= htmlspecialchars($device['donor_name']) ?></td>
              <td class="py-3"><?= $categoryLabels[$device['category']] ?? $device['category'] ?></td>
              <td class="py-3">
                <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $statusClasses[$device['status']] ?>">
                  <?= $statusLabels[$device['status']] ?? $device['status'] ?>
                </span>
              </td>
              <td class="py-3 text-text-muted text-sm"><?= date('Y-m-d', strtotime($device['created_at'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

</div>

</body>
</html>
