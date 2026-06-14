<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin', '../login.php?error=unauthorized');
$currentUser = getCurrentUser();
$csrf = generateCSRFToken();

$stmtPending = $pdo->prepare("SELECT r.*, d.name AS device_name, d.category, d.governorate AS device_governorate, u.full_name AS beneficiary_name, u.governorate AS beneficiary_governorate FROM requests r JOIN devices d ON r.device_id = d.id JOIN users u ON r.beneficiary_id = u.id WHERE r.status = 'pending' ORDER BY r.created_at DESC");
$stmtPending->execute();
$pendingRequests = $stmtPending->fetchAll();

$stmtArchived = $pdo->prepare("SELECT r.*, d.name AS device_name, d.category, d.governorate AS device_governorate, u.full_name AS beneficiary_name, u.governorate AS beneficiary_governorate FROM requests r JOIN devices d ON r.device_id = d.id JOIN users u ON r.beneficiary_id = u.id WHERE r.status != 'pending' ORDER BY r.created_at DESC");
$stmtArchived->execute();
$archivedRequests = $stmtArchived->fetchAll();

$categoryLabels = [
    'respiratory' => 'جهاز تنفسي',
    'mobility' => 'جهاز حركي',
    'beds_clinical' => 'أسرة وسريرية',
    'diagnostic' => 'تشخيصي',
];

$statusLabels = [
    'pending' => 'قيد الانتظار',
    'approved' => 'مقبول',
    'rejected' => 'مرفوض',
];
?><!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>مراجعة الطلبات | سند</title>
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
      <a href="index.php" class="text-text-muted hover:text-primary transition">الرئيسية</a>
      <a href="listings.php" class="text-text-muted hover:text-primary transition">الأجهزة</a>
      <a href="requests.php" class="text-primary font-semibold transition">الطلبات</a>
      <a href="users.php" class="text-text-muted hover:text-primary transition">المستخدمين</a>
      <a href="../marketplace.php" class="text-text-muted hover:text-primary transition">السوق</a>
      <a href="../logout.php" class="text-red-500 hover:text-red-700 transition">تسجيل الخروج</a>
    </div>
  </div>
</nav>

<div class="max-w-6xl mx-auto p-6 fade-in">

  <div class="flex items-center justify-between mb-8">
    <h1 class="text-2xl font-bold">مراجعة الطلبات</h1>
  </div>

  <div class="glass rounded-2xl p-6 overflow-x-auto mb-8">
    <?php if (empty($pendingRequests)): ?>
      <div class="text-center py-12">
        <p class="text-text-muted text-lg">لا توجد طلبات في انتظار المراجعة</p>
      </div>
    <?php else: ?>
      <table class="w-full text-right">
        <thead>
          <tr class="border-b border-gray-200">
            <th class="pb-3 font-semibold text-text-muted">الجهاز</th>
            <th class="pb-3 font-semibold text-text-muted">المستفيد</th>
            <th class="pb-3 font-semibold text-text-muted">محافظة المستفيد</th>
            <th class="pb-3 font-semibold text-text-muted">تاريخ التقديم</th>
            <th class="pb-3 font-semibold text-text-muted">الحالة</th>
            <th class="pb-3 font-semibold text-text-muted">الإجراءات</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pendingRequests as $r): ?>
            <tr class="border-b border-gray-100 hover:bg-white/50 transition">
              <td class="py-3 font-medium"><?= htmlspecialchars($r['device_name']) ?></td>
              <td class="py-3"><?= htmlspecialchars($r['beneficiary_name']) ?></td>
              <td class="py-3 text-text-muted text-sm"><?php $govs = getYemenGovernorates(); echo htmlspecialchars($govs[$r['beneficiary_governorate']] ?? $r['beneficiary_governorate']); ?></td>
              <td class="py-3 text-text-muted text-sm"><?= date('Y/m/d', strtotime($r['created_at'])) ?></td>
              <td class="py-3">
                <span class="status-pending px-3 py-1 rounded-full text-xs font-semibold">قيد الانتظار</span>
              </td>
              <td class="py-3">
                <div class="flex items-center gap-2 flex-wrap">
                  <button type="button" data-toggle-case="<?= $r['id'] ?>" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg text-sm font-semibold transition min-h-[44px]">عرض الحالة</button>
                  <form method="POST" action="action.php" style="display:inline">
                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                    <input type="hidden" name="action" value="approve_request">
                    <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition min-h-[44px]">قبول</button>
                  </form>
                  <form method="POST" action="action.php" style="display:inline" onsubmit="return confirm('تأكيد الرفض؟')">
                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                    <input type="hidden" name="action" value="reject_request">
                    <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                    <input type="text" name="rejection_reason" required placeholder="سبب الرفض" class="border rounded px-2 py-1 text-sm">
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition min-h-[44px]">رفض</button>
                  </form>
                </div>
                <div id="case-<?= $r['id'] ?>" class="hidden mt-4 p-4 border border-gray-200 rounded-xl bg-white/50">
                  <p class="text-sm font-semibold text-text-muted mb-2">وصف الحالة:</p>
                  <div class="p-3 border border-gray-200 rounded-lg bg-white mb-3 text-sm leading-relaxed"><?= nl2br(htmlspecialchars($r['case_description'])) ?></div>
                  <?php if ($r['medical_doc_path']): ?>
                    <a href="../serve-medical-doc.php?id=<?= $r['id'] ?>" target="_blank" class="text-primary hover:underline text-sm font-semibold">عرض المستند الطبي</a>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <?php if (!empty($archivedRequests)): ?>
    <details class="glass rounded-2xl p-6">
      <summary class="cursor-pointer font-semibold text-lg text-text-muted hover:text-primary transition mb-4">الطلبات المؤرشفة (<?= count($archivedRequests) ?>)</summary>
      <div class="overflow-x-auto">
        <table class="w-full text-right">
          <thead>
            <tr class="border-b border-gray-200">
              <th class="pb-3 font-semibold text-text-muted text-sm">الجهاز</th>
              <th class="pb-3 font-semibold text-text-muted text-sm">المستفيد</th>
              <th class="pb-3 font-semibold text-text-muted text-sm">تاريخ التقديم</th>
              <th class="pb-3 font-semibold text-text-muted text-sm">الحالة</th>
              <th class="pb-3 font-semibold text-text-muted text-sm">السبب</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($archivedRequests as $r): ?>
              <tr class="border-b border-gray-100 hover:bg-white/50 transition">
                <td class="py-2 text-sm font-medium"><?= htmlspecialchars($r['device_name']) ?></td>
                <td class="py-2 text-sm"><?= htmlspecialchars($r['beneficiary_name']) ?></td>
                <td class="py-2 text-text-muted text-sm"><?= date('Y/m/d', strtotime($r['created_at'])) ?></td>
                <td class="py-2">
                  <span class="status-<?= $r['status'] ?> px-3 py-1 rounded-full text-xs font-semibold"><?= $statusLabels[$r['status']] ?? $r['status'] ?></span>
                </td>
                <td class="py-2 text-sm text-text-muted"><?= ($r['status'] === 'rejected' && $r['rejection_reason']) ? htmlspecialchars($r['rejection_reason']) : '—' ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </details>
  <?php endif; ?>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('[data-toggle-case]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var el = document.getElementById('case-' + this.dataset.toggleCase);
      if (el) el.classList.toggle('hidden');
    });
  });
});
</script>

</body>
</html>
