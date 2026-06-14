<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
$csrf = generateCSRFToken();
$isBeneficiary = isLoggedIn() && getCurrentUser()['role'] === 'beneficiary';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT d.*, u.full_name AS donor_name, u.phone AS donor_phone, u.governorate AS donor_governorate
  FROM devices d
  JOIN users u ON u.id = d.donor_id
  WHERE d.id = ?");
$stmt->execute([$id]);
$device = $stmt->fetch();

if (!$device) {
  ?><!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>٤٠٤ | سند</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: { colors: { primary: '#00B4D8', 'primary-dark': '#0077A8', secondary: '#90E0EF', accent: '#CAF0F8', bg: '#F0F8FF', 'text-dark': '#1A1A2E', 'text-muted': '#6B7280' }, fontFamily: { tajawal: ['Tajawal', 'sans-serif'] } } }
    }
  </script>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="font-tajawal bg-bg text-text-dark min-h-screen">
  <div class="min-h-screen flex items-center justify-center">
    <div class="text-center">
      <h1 class="text-4xl font-bold text-text-muted mb-4">٤٠٤</h1>
      <p class="text-text-muted">الجهاز غير موجود</p>
      <a href="marketplace.php" class="mt-6 inline-block bg-primary text-white px-6 py-2 rounded-xl">العودة للسوق</a>
    </div>
  </div>
</body>
</html>
<?php
  exit;
}

$stmt = $pdo->prepare("SELECT * FROM device_photos WHERE device_id = ? ORDER BY is_primary DESC, id ASC");
$stmt->execute([$id]);
$photos = $stmt->fetchAll();

$governorates = getYemenGovernorates();
$govName = $governorates[$device['governorate']] ?? $device['governorate'];

$categoryLabels = [
  'respiratory' => 'جهاز تنفسي',
  'mobility' => 'جهاز حركي',
  'beds_clinical' => 'سرير طبي',
  'diagnostic' => 'جهاز تشخيصي',
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
$conditionColors = [
  'excellent' => 'badge-condition-excellent',
  'good' => 'badge-condition-good',
  'acceptable' => 'badge-condition-acceptable',
];
$statusLabels = [
  'pending_review' => 'قيد المراجعة',
  'active' => 'متاح',
  'under_request_review' => 'قيد الطلب',
  'loaned' => 'معار',
  'rejected' => 'مرفوض',
];
$statusColors = [
  'pending_review' => 'status-pending_review',
  'active' => 'status-active',
  'under_request_review' => 'status-under_request_review',
  'loaned' => 'status-loaned',
  'rejected' => 'status-rejected',
];

$mainPhoto = '';
$extraPhotos = [];
if (!empty($photos)) {
  $mainPhoto = $photos[0]['file_path'];
  $extraPhotos = array_slice($photos, 1);
}
$primaryImgSrc = $mainPhoto ?: 'assets/images/placeholder-device.svg';
?><!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($device['name']) ?> | سند</title>
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
  <script>
    var GOOGLE_MAPS_API_KEY = '<?= GOOGLE_MAPS_API_KEY ?>';
  </script>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="font-tajawal bg-bg text-text-dark min-h-screen">
  <nav class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
      <a href="index.php" class="text-2xl font-bold text-primary">سند</a>
      <div class="flex gap-4 items-center">
        <a href="marketplace.php" class="text-text-muted hover:text-primary transition text-sm">السوق</a>
        <?php if (isLoggedIn()): ?>
          <a href="logout.php" class="text-red-500 hover:text-red-700 transition text-sm">خروج</a>
        <?php else: ?>
          <a href="login.php" class="text-text-muted hover:text-primary transition text-sm">دخول</a>
          <a href="register.php" class="bg-primary text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-primary-dark transition">حساب جديد</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <div class="max-w-6xl mx-auto px-4 pt-6 text-sm text-text-muted">
    <a href="marketplace.php" class="hover:text-primary">السوق</a> / <span><?= htmlspecialchars($device['name']) ?></span>
  </div>

  <div class="max-w-6xl mx-auto p-4 grid grid-cols-1 lg:grid-cols-5 gap-8">
    <div class="lg:col-span-3">
      <div class="glass rounded-2xl overflow-hidden mb-6">
        <div class="relative">
          <img id="mainPhoto" src="<?= htmlspecialchars($primaryImgSrc) ?>" alt="<?= htmlspecialchars($device['name']) ?>" class="w-full h-64 md:h-96 object-cover" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 400 300%22%3E%3Crect fill=%22%23e5e7eb%22 width=%22400%22 height=%22300%22/%3E%3Ctext x=%22200%22 y=%22150%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%239ca3af%22 font-size=%2224%22%3E%D9%84%D8%A7 %D8%AA%D9%88%D8%AC%D8%AF %D8%B5%D9%88%D8%B1%D8%A9%3C/text%3E%3C/svg%3E'">
          <?php if (!empty($photos) && count($photos) > 1): ?>
          <div class="flex gap-2 p-4 overflow-x-auto">
            <?php foreach ($photos as $p): ?>
            <img src="<?= htmlspecialchars($p['file_path']) ?>" class="w-20 h-20 object-cover rounded-lg cursor-pointer border-2 border-transparent hover:border-primary transition" onclick="switchPhoto(this.src)" onerror="this.remove()">
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="glass rounded-2xl p-6 mb-6 fade-in">
        <h2 class="text-xl font-bold mb-4">الوصف</h2>
        <p class="text-text-muted leading-relaxed"><?= nl2br(htmlspecialchars($device['description'])) ?></p>
      </div>

      <div class="glass rounded-2xl p-6 fade-in">
        <h2 class="text-xl font-bold mb-4">الموقع</h2>
        <p class="text-text-muted mb-4">📍 <?= htmlspecialchars($govName) ?>، <?= htmlspecialchars($device['district']) ?></p>
        <div id="map" class="w-full h-64 rounded-xl bg-gray-100" data-lat="<?= $device['latitude'] ?>" data-lng="<?= $device['longitude'] ?>"></div>
      </div>
    </div>

    <div class="lg:col-span-2">
      <div class="glass rounded-2xl p-6 sticky top-4 fade-in">
        <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars($device['name']) ?></h1>

        <div class="space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-text-muted">التصنيف</span>
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-secondary/30 text-primary-dark"><?= $categoryLabels[$device['category']] ?? $device['category'] ?></span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-text-muted">الحالة</span>
            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $conditionColors[$device['condition_rating']] ?? 'badge-condition-acceptable' ?>"><?= $conditionLabels[$device['condition_rating']] ?? $device['condition_rating'] ?></span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-text-muted">نوع العرض</span>
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-accent text-primary-dark"><?= $offerLabels[$device['offer_type']] ?? $device['offer_type'] ?></span>
          </div>
          <?php if ($device['offer_type'] === 'loan' && $device['loan_duration']): ?>
          <div class="flex justify-between items-center">
            <span class="text-text-muted">مدة الإعارة</span>
            <span class="font-semibold"><?= htmlspecialchars($device['loan_duration']) ?></span>
          </div>
          <?php endif; ?>
          <div class="flex justify-between items-center">
            <span class="text-text-muted">المتبرع</span>
            <span class="font-semibold"><?= htmlspecialchars($device['donor_name']) ?></span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-text-muted">تاريخ الإضافة</span>
            <span class="text-sm"><?= date('Y/m/d', strtotime($device['created_at'])) ?></span>
          </div>
          <div class="pt-4 border-t border-gray-200">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold <?= $statusColors[$device['status']] ?? 'status-pending_review' ?>"><?= $statusLabels[$device['status']] ?? $device['status'] ?></span>
          </div>
        </div>

        <div class="mt-6 space-y-3">
          <?php if ($isBeneficiary && $device['status'] === 'active'): ?>
            <button type="button" id="requestBtn" onclick="openRequestModal()" class="block w-full text-center bg-primary hover:bg-primary-dark text-white py-3 rounded-xl font-semibold transition-all shadow-lg cursor-pointer">طلب هذا الجهاز</button>
          <?php endif; ?>
          <?php if ($isBeneficiary && $device['status'] !== 'active'): ?>
            <button disabled class="block w-full text-center bg-gray-300 text-gray-500 py-3 rounded-xl font-semibold cursor-not-allowed">الجهاز غير متاح حالياً</button>
          <?php endif; ?>
          <a href="marketplace.php" class="block text-center border border-primary text-primary hover:bg-primary hover:text-white py-3 rounded-xl font-semibold transition-all">العودة للسوق</a>
        </div>
      </div>
    </div>
  </div>

  <script>
    var photos = <?= json_encode(array_map(function($p) { return $p['file_path']; }, $photos)) ?>;
    var mainPhoto = document.getElementById('mainPhoto');
    function switchPhoto(src) { mainPhoto.src = src; }
  </script>
  <script src="assets/js/maps.js"></script>

<!-- Toast -->
<div id="toast" class="fixed bottom-6 right-6 z-50 hidden bg-green-50 text-green-700 border border-green-200 px-6 py-4 rounded-xl shadow-lg fade-in"></div>

<!-- Request Modal -->
<div id="requestModalOverlay" onclick="closeRequestModalOutside(event)" class="fixed inset-0 z-50 hidden items-center justify-center" style="background: rgba(0,0,0,0.5); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);">
  <div onclick="event.stopPropagation()" class="glass rounded-2xl p-6 w-full max-w-lg mx-4 shadow-2xl fade-in">
    <h2 class="text-xl font-bold mb-4">طلب الجهاز</h2>
    <form id="requestForm">
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
      <input type="hidden" name="device_id" value="<?= $device['id'] ?>">

      <div class="mb-4">
        <label for="case_description" class="block text-sm font-semibold text-text-dark mb-1">وصف الحالة</label>
        <textarea id="case_description" name="case_description" required minlength="50" maxlength="2000" rows="4" placeholder="اشرح حالتك الطبية واحتياجك للجهاز (50 حرفاً على الأقل)" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all resize-y"></textarea>
        <div class="flex justify-between mt-1 text-xs text-text-muted">
          <span id="caseDescCount">0</span>
          <span>2000</span>
        </div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-semibold text-text-dark mb-1">تقرير طبي</label>
        <input type="file" id="medical_report" name="medical_report" accept=".jpg,.jpeg,.png,.pdf" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary file:text-white file:font-semibold file:cursor-pointer file:hover:bg-primary-dark">
        <p class="text-xs text-text-muted mt-1">يرجى رفع تقرير طبي يوضح حالتك الصحية</p>
      </div>

      <div id="requestModalError" class="bg-red-50 text-red-600 p-3 rounded-xl text-sm mb-4 hidden border border-red-200"></div>

      <div class="flex gap-3">
        <button type="button" onclick="closeRequestModal()" class="flex-1 border border-gray-300 text-text-dark py-3 rounded-xl font-semibold transition-all hover:bg-gray-50">إلغاء</button>
        <button type="submit" id="requestSubmitBtn" class="flex-1 bg-primary hover:bg-primary-dark text-white py-3 rounded-xl font-semibold transition-all shadow-lg">إرسال الطلب</button>
      </div>
    </form>
  </div>
</div>

<script>
function openRequestModal() {
  document.getElementById('requestModalOverlay').classList.remove('hidden');
  document.getElementById('requestModalOverlay').classList.add('flex');
  document.getElementById('requestModalError').classList.add('hidden');
  document.getElementById('requestForm').reset();
  document.getElementById('caseDescCount').textContent = '0';
}
function closeRequestModal() {
  document.getElementById('requestModalOverlay').classList.add('hidden');
  document.getElementById('requestModalOverlay').classList.remove('flex');
}
function closeRequestModalOutside(e) {
  if (e.target === e.currentTarget) closeRequestModal();
}
function showToast(msg) {
  var el = document.getElementById('toast');
  el.textContent = msg;
  el.classList.remove('hidden');
  setTimeout(function() { el.classList.add('hidden'); }, 4000);
}
document.addEventListener('DOMContentLoaded', function() {
  var caseDesc = document.getElementById('case_description');
  if (caseDesc) {
    caseDesc.addEventListener('input', function() {
      document.getElementById('caseDescCount').textContent = this.value.length;
    });
  }
  document.getElementById('requestForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var errorEl = document.getElementById('requestModalError');
    errorEl.classList.add('hidden');
    var submitBtn = document.getElementById('requestSubmitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'جاري الإرسال...';
    var formData = new FormData(this);
    fetch('request.php', { method: 'POST', body: formData })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'إرسال الطلب';
        if (data.success) {
          closeRequestModal();
          var btn = document.getElementById('requestBtn');
          if (btn) {
            btn.outerHTML = '<button disabled class="block w-full text-center bg-gray-300 text-gray-500 py-3 rounded-xl font-semibold cursor-not-allowed">الجهاز غير متاح حالياً</button>';
          }
          showToast('تم إرسال طلبك بنجاح! سيتم مراجعته من قبل الإدارة.');
        } else {
          errorEl.textContent = data.error || 'حدث خطأ غير متوقع';
          errorEl.classList.remove('hidden');
        }
      })
      .catch(function() {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'إرسال الطلب';
        errorEl.textContent = 'حدث خطأ في الاتصال. حاول مرة أخرى.';
        errorEl.classList.remove('hidden');
      });
  });
});
</script>
</body>
</html>
