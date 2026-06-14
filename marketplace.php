<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
$isBeneficiary = isLoggedIn() && getCurrentUser()['role'] === 'beneficiary';
$currentUser = getCurrentUser();
$csrf = generateCSRFToken();

$stmt = $pdo->prepare("SELECT d.*, dp.file_path AS primary_photo 
  FROM devices d 
  LEFT JOIN device_photos dp ON dp.device_id = d.id AND dp.is_primary = 1
  WHERE d.status = 'active' 
  ORDER BY d.created_at DESC");
$stmt->execute();
$devices = $stmt->fetchAll();

$govs = getYemenGovernorates();
$districtsData = [];
foreach ($govs as $key => $name) {
    $districtsData[$key] = getDistricts($key);
}

$categoryLabels = [
    'respiratory' => 'تنفسي',
    'mobility' => 'حركي',
    'beds_clinical' => 'أسرة ومستلزمات سريرية',
    'diagnostic' => 'تشخيصي',
];

$conditionLabels = [
    'excellent' => 'ممتاز',
    'good' => 'جيد',
    'acceptable' => 'مقبول',
];

$offerLabels = [
    'donation' => 'تبرع',
    'loan' => 'إعارة',
];
?><!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>السوق | سند</title>
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
  <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
    <a href="index.php"><img src="assets/images/logo.svg" alt="سند" class="h-10"></a>
    <div class="flex gap-4 items-center">
      <a href="marketplace.php" class="text-primary font-semibold">السوق</a>
      <?php if (isLoggedIn()): ?>
        <?php if ($currentUser && $currentUser['role'] === 'beneficiary'): ?>
          <a href="dashboard-beneficiary.php" class="text-text-muted hover:text-primary transition">طلباتي</a>
        <?php elseif ($currentUser && $currentUser['role'] === 'donor'): ?>
          <a href="dashboard-donor.php" class="text-text-muted hover:text-primary transition">لوحة التحكم</a>
        <?php elseif ($currentUser && $currentUser['role'] === 'admin'): ?>
          <a href="admin/index.php" class="text-text-muted hover:text-primary transition">لوحة التحكم</a>
        <?php endif; ?>
        <a href="logout.php" class="text-text-muted hover:text-primary transition">خروج</a>
      <?php else: ?>
        <a href="login.php" class="text-text-muted hover:text-primary transition">دخول</a>
        <a href="register.php" class="bg-primary text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-primary-dark transition">حساب جديد</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="max-w-7xl mx-auto p-6">
  <div class="flex flex-col lg:flex-row gap-6">
    <!-- Sidebar - Right side (first in DOM = right in RTL flex) -->
    <aside class="lg:w-72 flex-shrink-0">
      <div class="glass rounded-2xl p-5 sticky top-24">
        <h2 class="font-bold text-lg mb-4">تصفية</h2>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-semibold text-text-dark mb-1">المحافظة</label>
            <select id="filterGovernorate" class="filter-select w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white">
              <option value="">الكل</option>
              <?php foreach ($govs as $key => $name): ?>
                <option value="<?= $key ?>"><?= $name ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold text-text-dark mb-1">المديرية</label>
            <select id="filterDistrict" class="filter-select w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white" disabled>
              <option value="">اختر المحافظة أولاً</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold text-text-dark mb-1">التصنيف الطبي</label>
            <select id="filterCategory" class="filter-select w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white">
              <option value="">الكل</option>
              <option value="respiratory">تنفسي</option>
              <option value="mobility">حركي</option>
              <option value="beds_clinical">أسرة ومستلزمات سريرية</option>
              <option value="diagnostic">تشخيصي</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold text-text-dark mb-1">نوع العرض</label>
            <select id="filterOffer" class="filter-select w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white">
              <option value="">الكل</option>
              <option value="donation">تبرع</option>
              <option value="loan">إعارة</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold text-text-dark mb-1">حالة الجهاز</label>
            <select id="filterCondition" class="filter-select w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white">
              <option value="">الكل</option>
              <option value="excellent">ممتاز</option>
              <option value="good">جيد</option>
              <option value="acceptable">مقبول</option>
            </select>
          </div>

          <button id="clearFilters" class="text-sm text-red-500 hover:text-red-700 transition w-full text-center pt-2">مسح الكل</button>
        </div>
      </div>
    </aside>

    <!-- Main Content - Left side -->
    <main class="flex-1 min-w-0">
      <div class="relative mb-4">
        <input type="text" id="searchInput" placeholder="ابحث عن جهاز..." class="w-full px-5 py-3 pr-12 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white">
        <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      </div>

      <p id="resultsCount" class="text-text-muted text-sm mb-4"><?= count($devices) ?> نتائج</p>

      <?php if (empty($devices)): ?>
        <div class="text-center py-16">
          <p class="text-text-muted text-lg">لا توجد أجهزة متاحة حالياً</p>
        </div>
      <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
          <?php foreach ($devices as $device):
            $govName = $govs[$device['governorate']] ?? $device['governorate'];
            $catLabel = $categoryLabels[$device['category']] ?? $device['category'];
            $condLabel = $conditionLabels[$device['condition_rating']] ?? $device['condition_rating'];
            $offerLabel = $offerLabels[$device['offer_type']] ?? $device['offer_type'];

            $condClass = '';
            if ($device['condition_rating'] === 'excellent') $condClass = 'bg-green-100 text-green-700';
            elseif ($device['condition_rating'] === 'good') $condClass = 'bg-blue-100 text-blue-700';
            elseif ($device['condition_rating'] === 'acceptable') $condClass = 'bg-yellow-100 text-yellow-700';
          ?>
            <div class="device-card glass rounded-2xl overflow-hidden hover:-translate-y-1 hover:shadow-lg transition-all duration-300 fade-in"
                 data-category="<?= $device['category'] ?>"
                 data-condition="<?= $device['condition_rating'] ?>"
                 data-offer="<?= $device['offer_type'] ?>"
                 data-governorate="<?= $device['governorate'] ?>"
                 data-district="<?= htmlspecialchars($device['district']) ?>"
                 data-status="<?= $device['status'] ?>">
              <div class="aspect-[4/3] bg-gray-100 overflow-hidden">
                <img src="<?= $device['primary_photo'] ? htmlspecialchars($device['primary_photo']) : 'assets/images/placeholder-device.svg' ?>"
                     alt="<?= htmlspecialchars($device['name']) ?>"
                     class="w-full h-full object-cover">
              </div>
              <div class="p-4">
                <h3 class="font-bold text-lg mb-2"><?= htmlspecialchars($device['name']) ?></h3>
                <div class="flex flex-wrap gap-2 mb-3">
                  <span class="px-3 py-1 rounded-full text-xs font-semibold bg-secondary/30 text-primary-dark"><?= $catLabel ?></span>
                  <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $condClass ?>"><?= $condLabel ?></span>
                  <span class="px-3 py-1 rounded-full text-xs font-semibold bg-accent text-primary-dark"><?= $offerLabel ?></span>
                </div>
                <p class="text-text-muted text-sm mb-2">📍 <?= $govName ?>، <?= htmlspecialchars($device['district']) ?></p>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">متاح</span>
                <?php if ($isBeneficiary): ?>
                  <a href="device.php?id=<?= $device['id'] ?>" class="mt-3 block text-center bg-primary hover:bg-primary-dark text-white py-2 rounded-xl font-semibold transition text-sm min-h-[44px] flex items-center justify-center">طلب الجهاز</a>
                <?php endif; ?>
                <?php if (!$isBeneficiary): ?>
                  <a href="device.php?id=<?= $device['id'] ?>" class="mt-3 block text-center border border-primary text-primary hover:bg-primary hover:text-white py-2 rounded-xl font-semibold transition text-sm min-h-[44px] flex items-center justify-center">عرض التفاصيل</a>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </main>
  </div>
</div>

<script>
  window.districts = <?= json_encode($districtsData, JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="assets/js/main.js"></script>
</body>
</html>
