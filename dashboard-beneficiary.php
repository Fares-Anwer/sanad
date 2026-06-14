<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireRole('beneficiary');
$currentUser = getCurrentUser();
$csrf = generateCSRFToken();

$stmt = $pdo->prepare("
    SELECT r.*, d.name AS device_name
    FROM requests r
    JOIN devices d ON d.id = r.device_id
    WHERE r.beneficiary_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$currentUser['id']]);
$requests = $stmt->fetchAll();

$statusLabels = [
    'pending' => 'قيد المراجعة',
    'approved' => 'تمت الموافقة',
    'rejected' => 'مرفوض',
];
$statusClasses = [
    'pending' => 'status-pending',
    'approved' => 'status-approved',
    'rejected' => 'status-rejected',
];
?><!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>لوحة التحكم — المستفيد | سند</title>
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
  <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
    <div class="max-w-4xl mx-auto px-4 h-16 flex items-center justify-between">
      <a href="index.php"><img src="assets/images/logo.svg" alt="سند" class="h-10"></a>
      <div class="flex gap-4 items-center">
        <a href="marketplace.php" class="text-text-muted hover:text-primary transition">السوق</a>
        <a href="logout.php" class="text-red-500 hover:text-red-700 transition">تسجيل الخروج</a>
      </div>
    </div>
  </nav>
  <div class="max-w-4xl mx-auto p-6 fade-in">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-2xl font-bold">مرحباً، <?= htmlspecialchars($currentUser['full_name']) ?></h1>
        <p class="text-text-muted text-sm mt-1">لوحة طلباتي</p>
      </div>

    <?php if (empty($requests)): ?>
      <div class="glass rounded-2xl p-8 text-center">
        <p class="text-text-muted text-lg mb-2">لا توجد طلبات بعد</p>
        <p class="text-text-muted text-sm mb-6">تصفح الأجهزة المتاحة في السوق وقدم طلبك للحصول على الدعم</p>
        <a href="marketplace.php" class="inline-block bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl">
          تصفح الأجهزة
        </a>
      </div>
    <?php else: ?>
      <div class="mb-6">
        <a href="marketplace.php" class="text-primary hover:text-primary-dark font-semibold text-sm transition">&rarr; العودة إلى السوق</a>
      </div>

      <div class="space-y-4">
        <?php foreach ($requests as $req): ?>
          <div class="glass rounded-2xl p-4 card-hover">
            <div class="flex items-start justify-between gap-4">
              <div class="flex-1 min-w-0">
                <a href="device.php?id=<?= $req['device_id'] ?>" class="text-lg font-semibold text-text-dark hover:text-primary transition">
                  <?= htmlspecialchars($req['device_name']) ?>
                </a>
                <p class="text-text-muted text-sm mt-1">تاريخ الطلب: <?= date('Y/m/d', strtotime($req['created_at'])) ?></p>
              </div>
              <span class="px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap <?= $statusClasses[$req['status']] ?>">
                <?= $statusLabels[$req['status']] ?>
              </span>
            </div>
            <?php if ($req['status'] === 'rejected' && $req['rejection_reason']): ?>
              <div class="mt-3 text-sm text-red-600 bg-red-50 rounded-xl p-3">
                <span class="font-semibold">سبب الرفض:</span> <?= htmlspecialchars($req['rejection_reason']) ?>
              </div>
            <?php elseif ($req['status'] === 'approved'):
              $stmt = $pdo->prepare("SELECT u.full_name, u.governorate, u.phone
                                     FROM requests r
                                     JOIN devices d ON r.device_id = d.id
                                     JOIN users u ON d.donor_id = u.id
                                     WHERE r.id = ?");
              $stmt->execute([$req['id']]);
              $donor = $stmt->fetch();
              if ($donor):
                $donorPhone = formatYemeniPhone($donor['phone']);
                $deviceName = htmlspecialchars($req['device_name']);
                $beneficiaryName = htmlspecialchars($currentUser['full_name']);
                $donorName = htmlspecialchars($donor['full_name']);
                $governorates = getYemenGovernorates();
                $govLabel = $governorates[$donor['governorate']] ?? htmlspecialchars($donor['governorate']);
                $waMsg = "السلام عليكم، أنا {$beneficiaryName}، تواصلت معكم بخصوص جهاز {$deviceName} حسب إعلانكم في منصة سند. أرجو الإفادة عن كيفية الاستلام.";
                $waUrl = generateWhatsAppUrl($donorPhone, $waMsg);
              ?>
              <div class="mt-3 bg-white/50 rounded-xl p-3 border border-primary/20">
                <p class="text-sm text-text-dark"><span class="font-semibold">المتبرع:</span> <?= $donorName ?> — <?= $govLabel ?></p>
                <div class="flex gap-2 mt-2">
                  <a href="tel:+<?= $donorPhone ?>" class="flex-1 inline-flex items-center justify-center gap-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    <i class="fas fa-phone"></i> اتصل بالمتبرع
                  </a>
                  <a href="<?= $waUrl ?>" target="_blank" class="flex-1 inline-flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    <i class="fab fa-whatsapp"></i> واتساب
                  </a>
                </div>
              </div>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
