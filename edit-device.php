<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$user = getCurrentUser();
if (!$user || !in_array($user['role'], ['donor', 'admin'])) {
    redirect('login.php?error=unauthorized');
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM devices WHERE id = ?");
$stmt->execute([$id]);
$device = $stmt->fetch();

if (!$device) {
    redirect('marketplace.php?error=not_found');
}

// Permission check: only the donor who added the device OR an admin can edit it!
if ($device['donor_id'] !== $user['id'] && $user['role'] !== 'admin') {
    redirect('marketplace.php?error=unauthorized');
}

$csrf = generateCSRFToken();
$error = '';
$success = '';

$govs = getYemenGovernorates();
$districtsData = [];
foreach ($govs as $key => $name) {
    $districtsData[$key] = getDistricts($key);
}

$categories = [
    'respiratory' => 'جهاز تنفسي',
    'mobility' => 'جهاز حركي',
    'beds_clinical' => 'أسرة ومستلزمات سريرية',
    'diagnostic' => 'أجهزة تشخيصية',
];

$conditions = [
    'excellent' => 'ممتاز',
    'good' => 'جيد',
    'acceptable' => 'مقبول',
];

$offerTypes = [
    'donation' => 'تبرع دائم',
    'loan' => 'إعارة مؤقتة',
];

$loanDurations = [
    '2_weeks' => 'أسبوعين',
    '1_month' => 'شهر',
    '3_months' => '3 أشهر',
    '6_months' => '6 أشهر',
    'negotiable' => 'قابل للتفاوض',
];

// Fetch current photos
$photoStmt = $pdo->prepare("SELECT * FROM device_photos WHERE device_id = ? ORDER BY is_primary DESC, id ASC");
$photoStmt->execute([$id]);
$photos = $photoStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'رمز غير صالح. حاول مرة أخرى.';
    } else {
        $name = sanitizeInput($_POST['name'] ?? '');
        $category = $_POST['category'] ?? '';
        $conditionRating = $_POST['condition_rating'] ?? '';
        $description = sanitizeTextarea($_POST['description'] ?? '');
        $offerType = $_POST['offer_type'] ?? '';
        $loanDuration = sanitizeInput($_POST['loan_duration'] ?? '');
        $governorate = $_POST['governorate'] ?? '';
        $district = sanitizeInput($_POST['district'] ?? '');
        $latitude = !empty($_POST['latitude']) ? $_POST['latitude'] : null;
        $longitude = !empty($_POST['longitude']) ? $_POST['longitude'] : null;
        $deletePhotoIds = $_POST['delete_photos'] ?? [];

        $errors = [];

        $nameLen = mb_strlen($name);
        if ($nameLen < 1 || $nameLen > 150) {
            $errors[] = 'اسم الجهاز يجب أن يكون بين 1 و 150 حرفاً.';
        }

        if (!array_key_exists($category, $categories)) {
            $errors[] = 'التصنيف الطبي غير صحيح.';
        }

        if (!array_key_exists($conditionRating, $conditions)) {
            $errors[] = 'حالة الجهاز غير صحيحة.';
        }

        $descLen = mb_strlen($description);
        if ($descLen < 30 || $descLen > 2000) {
            $errors[] = 'الوصف يجب أن يكون بين 30 و 2000 حرف.';
        }

        if (!array_key_exists($offerType, $offerTypes)) {
            $errors[] = 'نوع التبرع غير صحيح.';
        }

        if ($offerType === 'loan' && (empty($loanDuration) || !array_key_exists($loanDuration, $loanDurations))) {
            $errors[] = 'يرجى اختيار مدة الإعارة.';
        }

        if (!array_key_exists($governorate, $govs)) {
            $errors[] = 'يرجى اختيار المحافظة.';
        }

        $validDistricts = getDistricts($governorate);
        if (!in_array($district, $validDistricts)) {
            $errors[] = 'يرجى اختيار المديرية.';
        }

        // Photo count validation
        $currentPhotoCount = count($photos);
        $deletePhotoCount = count($deletePhotoIds);
        
        $newPhotoCount = 0;
        if (isset($_FILES['photos']) && !empty($_FILES['photos']['tmp_name'][0])) {
            $newPhotoCount = count($_FILES['photos']['tmp_name']);
        }

        $netPhotoCount = $currentPhotoCount - $deletePhotoCount + $newPhotoCount;

        if ($netPhotoCount < 1) {
            $errors[] = 'يجب أن يحتوي الجهاز على صورة واحدة على الأقل.';
        } elseif ($netPhotoCount > 6) {
            $errors[] = 'الحد الأقصى للصور هو 6 صور.';
        }

        if (empty($errors)) {
            $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'devices' . DIRECTORY_SEPARATOR;
            $uploadedPaths = [];
            $fileErrors = [];

            // Handle new file uploads
            if ($newPhotoCount > 0) {
                $files = $_FILES['photos'];
                for ($i = 0; $i < $newPhotoCount; $i++) {
                    if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                        $fileErrors[] = 'خطأ في رفع الصورة رقم ' . ($i + 1) . '.';
                        continue;
                    }

                    if ($files['size'][$i] > UPLOAD_MAX_SIZE) {
                        $fileErrors[] = 'الصورة رقم ' . ($i + 1) . ' أكبر من 5 ميجابايت.';
                        continue;
                    }

                    if (!isAllowedExtension($files['name'][$i])) {
                        $fileErrors[] = 'امتداد الصورة رقم ' . ($i + 1) . ' غير مسموح.';
                        continue;
                    }

                    if (!validateFileMIME($files['tmp_name'][$i])) {
                        $fileErrors[] = 'الصورة رقم ' . ($i + 1) . ' من نوع غير مسموح.';
                        continue;
                    }

                    $ext = getFileExtension($files['name'][$i]);
                    $newName = generateUUID() . '.' . $ext;
                    $destPath = $uploadDir . $newName;

                    if (!move_uploaded_file($files['tmp_name'][$i], $destPath)) {
                        $fileErrors[] = 'فشل في رفع الصورة رقم ' . ($i + 1) . '.';
                        continue;
                    }

                    $uploadedPaths[] = 'uploads/devices/' . $newName;
                }
            }

            if (!empty($fileErrors)) {
                $error = implode('<br>', $fileErrors);
                foreach ($uploadedPaths as $path) {
                    @unlink(__DIR__ . DIRECTORY_SEPARATOR . $path);
                }
            } else {
                try {
                    $pdo->beginTransaction();

                    // 1. Process deletions
                    $deletedPrimary = false;
                    if ($deletePhotoCount > 0) {
                        // Find if primary is being deleted
                        foreach ($photos as $p) {
                            if (in_array($p['id'], $deletePhotoIds)) {
                                if ($p['is_primary'] == 1) {
                                    $deletedPrimary = true;
                                }
                                // Delete physical file
                                @unlink(__DIR__ . DIRECTORY_SEPARATOR . $p['file_path']);
                            }
                        }

                        // Delete DB rows
                        $inQuery = implode(',', array_fill(0, $deletePhotoCount, '?'));
                        $delStmt = $pdo->prepare("DELETE FROM device_photos WHERE id IN ($inQuery) AND device_id = ?");
                        $delParams = array_merge($deletePhotoIds, [$id]);
                        $delStmt->execute($delParams);
                    }

                    // 2. Insert new photos
                    $stmtPhoto = $pdo->prepare("INSERT INTO device_photos (device_id, file_path, is_primary) VALUES (?, ?, ?)");
                    foreach ($uploadedPaths as $path) {
                        $stmtPhoto->execute([$id, $path, 0]);
                    }

                    // 3. Handle primary photo reallocation if primary was deleted
                    if ($deletedPrimary) {
                        // Set the oldest remaining photo as primary
                        $selStmt = $pdo->prepare("SELECT id FROM device_photos WHERE device_id = ? ORDER BY id ASC LIMIT 1");
                        $selStmt->execute([$id]);
                        $newPrimaryId = $selStmt->fetchColumn();
                        if ($newPrimaryId) {
                            $updPrimaryStmt = $pdo->prepare("UPDATE device_photos SET is_primary = 1 WHERE id = ?");
                            $updPrimaryStmt->execute([$newPrimaryId]);
                        }
                    }

                    // If no photos were primary (e.g. no primary deleted, but somehow none is primary), set first as primary
                    $checkPrimaryStmt = $pdo->prepare("SELECT COUNT(*) FROM device_photos WHERE device_id = ? AND is_primary = 1");
                    $checkPrimaryStmt->execute([$id]);
                    if ($checkPrimaryStmt->fetchColumn() == 0) {
                        $selStmt = $pdo->prepare("SELECT id FROM device_photos WHERE device_id = ? ORDER BY id ASC LIMIT 1");
                        $selStmt->execute([$id]);
                        $firstId = $selStmt->fetchColumn();
                        if ($firstId) {
                            $updPrimaryStmt = $pdo->prepare("UPDATE device_photos SET is_primary = 1 WHERE id = ?");
                            $updPrimaryStmt->execute([$firstId]);
                        }
                    }

                    // 4. Update device details
                    // Donors edits will reset status to pending_review; admins edits keep status
                    $newStatus = ($user['role'] === 'admin') ? $device['status'] : 'pending_review';

                    $stmt = $pdo->prepare("UPDATE devices SET name = ?, category = ?, condition_rating = ?, description = ?, offer_type = ?, loan_duration = ?, governorate = ?, district = ?, latitude = ?, longitude = ?, status = ? WHERE id = ?");
                    $stmt->execute([$name, $category, $conditionRating, $description, $offerType, $offerType === 'loan' ? $loanDuration : null, $governorate, $district, $latitude, $longitude, $newStatus, $id]);

                    $pdo->commit();
                    $_SESSION['edit_device_success'] = 'تم تحديث معلومات الجهاز بنجاح!' . ($user['role'] !== 'admin' ? ' سيتم مراجعة التعديلات من قبل الإدارة.' : '');
                    redirect('edit-device.php?id=' . $id);
                } catch (Exception $e) {
                    $pdo->rollBack();
                    foreach ($uploadedPaths as $path) {
                        @unlink(__DIR__ . DIRECTORY_SEPARATOR . $path);
                    }
                    $error = 'حدث خطأ أثناء حفظ البيانات: ' . $e->getMessage();
                }
            }
        } else {
            $error = implode('<br>', $errors);
        }
    }
}

if (isset($_SESSION['edit_device_success'])) {
    $success = $_SESSION['edit_device_success'];
    unset($_SESSION['edit_device_success']);
}

// Fetch photos again in case they were updated
$photoStmt->execute([$id]);
$photos = $photoStmt->fetchAll();
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تعديل معلومات الجهاز | سند</title>
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
  <link rel="stylesheet" href="<?= LEAFLET_CSS ?>">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="font-tajawal bg-bg text-text-dark min-h-screen">

<nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
  <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
    <a href="index.php"><img src="assets/images/logo.svg" alt="سند" class="h-10"></a>
    <div class="flex gap-4 items-center">
      <a href="marketplace.php" class="text-text-muted hover:text-primary transition">السوق</a>
      <a href="<?= $user['role'] === 'admin' ? 'admin/index.php' : 'dashboard-donor.php' ?>" class="text-text-muted hover:text-primary transition">لوحة التحكم</a>
      <a href="logout.php" class="text-text-muted hover:text-primary transition">خروج</a>
    </div>
  </div>
</nav>

<div class="max-w-2xl mx-auto p-6">
  <?php if ($success): ?>
    <div class="bg-green-50 text-green-700 p-4 rounded-xl text-center mb-6 fade-in border border-green-200">
      <?= $success ?>
    </div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm mb-6 border border-red-200 fade-in"><?= $error ?></div>
  <?php endif; ?>

  <div class="glass rounded-2xl p-8 shadow-xl fade-in">
    <div class="text-center mb-8">
      <h1 class="text-3xl font-bold text-primary">تعديل معلومات الجهاز</h1>
      <p class="text-text-muted mt-2">قم بتحديث تفاصيل إعلان جهازك الطبي</p>
    </div>

    <form method="POST" action="" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

      <div class="mb-5">
        <label for="name" class="block text-sm font-semibold text-text-dark mb-1">اسم الجهاز</label>
        <input type="text" id="name" name="name" required maxlength="150" value="<?= htmlspecialchars($device['name']) ?>" placeholder="أدخل اسم الجهاز الطبي" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
      </div>

      <div class="mb-5">
        <label for="category" class="block text-sm font-semibold text-text-dark mb-1">التصنيف الطبي</label>
        <select id="category" name="category" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white">
          <option value="">اختر التصنيف</option>
          <?php foreach ($categories as $key => $label): ?>
            <option value="<?= $key ?>" <?= $device['category'] === $key ? 'selected' : '' ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-5">
        <span class="block text-sm font-semibold text-text-dark mb-2">حالة الجهاز</span>
        <div class="grid grid-cols-3 gap-3">
          <?php foreach ($conditions as $key => $label): ?>
            <label class="flex items-center justify-center gap-2 p-3 rounded-xl border-2 border-gray-200 cursor-pointer transition-all has-[:checked]:border-primary has-[:checked]:bg-primary/5">
              <input type="radio" name="condition_rating" value="<?= $key ?>" <?= $device['condition_rating'] === $key ? 'checked' : '' ?> class="appearance-none w-4 h-4 rounded-full border-2 border-gray-300 checked:border-primary checked:bg-primary checked:ring-2 checked:ring-primary/20 transition-all" required>
              <span class="text-sm font-medium"><?= $label ?></span>
            </label>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="mb-5">
        <label for="description" class="block text-sm font-semibold text-text-dark mb-1">الوصف</label>
        <textarea id="description" name="description" required minlength="30" maxlength="2000" rows="5" placeholder="اكتب وصفاً تفصيلياً للجهاز (30 حرفاً على الأقل)" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all resize-y"><?= htmlspecialchars($device['description']) ?></textarea>
        <div class="flex justify-between mt-1 text-xs text-text-muted">
          <span id="descCount"><?= mb_strlen($device['description']) ?></span>
          <span>2000</span>
        </div>
      </div>

      <div class="mb-5">
        <span class="block text-sm font-semibold text-text-dark mb-2">نوع التبرع</span>
        <div class="grid grid-cols-2 gap-3">
          <?php foreach ($offerTypes as $key => $label): ?>
            <label class="flex items-center justify-center gap-2 p-3 rounded-xl border-2 border-gray-200 cursor-pointer transition-all has-[:checked]:border-primary has-[:checked]:bg-primary/5">
              <input type="radio" name="offer_type" value="<?= $key ?>" <?= $device['offer_type'] === $key ? 'checked' : '' ?> class="appearance-none w-4 h-4 rounded-full border-2 border-gray-300 checked:border-primary checked:bg-primary checked:ring-2 checked:ring-primary/20 transition-all" required>
              <span class="text-sm font-medium"><?= $label ?></span>
            </label>
          <?php endforeach; ?>
        </div>
      </div>

      <div id="loanDurationGroup" class="mb-5 <?= $device['offer_type'] === 'loan' ? '' : 'hidden' ?>">
        <label for="loan_duration" class="block text-sm font-semibold text-text-dark mb-1">مدة الإعارة</label>
        <select id="loan_duration" name="loan_duration" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white">
          <option value="">اختر المدة</option>
          <?php foreach ($loanDurations as $key => $label): ?>
            <option value="<?= $key ?>" <?= $device['loan_duration'] === $key ? 'selected' : '' ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-5">
        <label for="governorate" class="block text-sm font-semibold text-text-dark mb-1">المحافظة</label>
        <select id="governorate" name="governorate" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white">
          <option value="">اختر المحافظة</option>
          <?php foreach ($govs as $key => $name): ?>
            <option value="<?= $key ?>" <?= $device['governorate'] === $key ? 'selected' : '' ?>><?= $name ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-5">
        <label for="district" class="block text-sm font-semibold text-text-dark mb-1">المديرية</label>
        <select id="district" name="district" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white">
          <!-- Populated by JS on load -->
        </select>
      </div>

      <div class="mb-5">
        <span class="block text-sm font-semibold text-text-dark mb-1">الموقع على الخريطة</span>
        <div class="relative mb-2">
          <input type="text" id="locationSearch" placeholder="ابحث عن موقع..." class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all text-sm">
          <div id="searchResults" class="absolute z-50 top-full mt-1 left-0 right-0 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto hidden"></div>
        </div>
        <div id="mapPicker" class="w-full rounded-xl bg-gray-100" style="height: 300px;" data-lat="<?= $device['latitude'] ?>" data-lng="<?= $device['longitude'] ?>"></div>
        <input type="hidden" name="latitude" id="latitude" value="<?= $device['latitude'] ?>">
        <input type="hidden" name="longitude" id="longitude" value="<?= $device['longitude'] ?>">
        <div id="manualCoords" class="hidden grid grid-cols-2 gap-3 mt-3">
          <div>
            <label for="manualLat" class="block text-xs font-semibold text-text-muted mb-1">خط العرض</label>
            <input type="text" id="manualLat" value="<?= $device['latitude'] ?>" class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all text-sm" placeholder="مثال: 15.5527">
          </div>
          <div>
            <label for="manualLng" class="block text-xs font-semibold text-text-muted mb-1">خط الطول</label>
            <input type="text" id="manualLng" value="<?= $device['longitude'] ?>" class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all text-sm" placeholder="مثال: 48.5164">
          </div>
        </div>
        <p class="text-xs text-text-muted mt-1">ابحث عن موقعك أو انقر على الخريطة لتحديد الموقع (اختياري)</p>
      </div>

      <div class="mb-5">
        <span class="block text-sm font-semibold text-text-dark mb-2 font-tajawal">الصور الحالية</span>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-4">
          <?php foreach ($photos as $photo): ?>
            <div class="relative rounded-xl overflow-hidden border border-gray-200 bg-white shadow-sm flex flex-col justify-between">
              <img src="<?= htmlspecialchars($photo['file_path']) ?>" class="w-full h-28 object-cover" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 200 150%22%3E%3Crect fill=%22%23e5e7eb%22 width=%22200%22 height=%22150%22/%3E%3C/svg%3E'">
              <div class="p-2 bg-gray-50 flex items-center gap-2 border-t border-gray-100">
                <input type="checkbox" name="delete_photos[]" value="<?= $photo['id'] ?>" id="del_img_<?= $photo['id'] ?>" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                <label for="del_img_<?= $photo['id'] ?>" class="text-xs font-medium text-red-600 cursor-pointer select-none">حذف الصورة</label>
                <?php if ($photo['is_primary']): ?>
                  <span class="text-[10px] bg-primary/10 text-primary-dark px-1.5 py-0.5 rounded mr-auto font-semibold">رئيسية</span>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="mb-6">
        <label for="photos" class="block text-sm font-semibold text-text-dark mb-1">إضافة صور جديدة</label>
        <input type="file" id="photos" name="photos[]" multiple accept=".jpg,.jpeg,.png,.webp" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white file:ml-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary file:text-white file:font-semibold file:cursor-pointer file:hover:bg-primary-dark">
        <div id="photoPreview" class="mt-4"></div>
        <p class="text-xs text-text-muted mt-1">الحد الأقصى الإجمالي للصور هو 6 صور.</p>
      </div>

      <button type="submit" class="bg-primary hover:bg-primary-dark text-white w-full py-3 rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl text-lg">حفظ التغييرات</button>
    </form>

    <p class="text-center text-text-muted text-sm mt-6">
      <a href="<?= $user['role'] === 'admin' ? 'admin/index.php' : 'dashboard-donor.php' ?>" class="text-primary hover:text-primary-dark font-semibold">العودة إلى لوحة التحكم</a>
    </p>
  </div>
</div>

<script>
  window.districts = <?= json_encode($districtsData, JSON_UNESCAPED_UNICODE) ?>;
  var currentDistrict = <?= json_encode($device['district'], JSON_UNESCAPED_UNICODE) ?>;

  function populateDistricts() {
    var govSelect = document.getElementById('governorate');
    var distSelect = document.getElementById('district');
    var selectedGov = govSelect.value;

    distSelect.innerHTML = '';
    distSelect.disabled = true;

    if (!selectedGov || !window.districts[selectedGov]) {
      distSelect.innerHTML = '<option value="">اختر المحافظة أولاً</option>';
      return;
    }

    distSelect.disabled = false;
    distSelect.innerHTML = '<option value="">اختر المديرية</option>';

    window.districts[selectedGov].forEach(function(district) {
      var opt = document.createElement('option');
      opt.value = district;
      opt.textContent = district;
      if (district === currentDistrict) {
        opt.selected = true;
      }
      distSelect.appendChild(opt);
    });
  }

  document.getElementById('governorate').addEventListener('change', populateDistricts);
  
  // Trigger initial populate
  populateDistricts();

  document.querySelectorAll('input[name="offer_type"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
      var group = document.getElementById('loanDurationGroup');
      if (this.value === 'loan') {
        group.classList.remove('hidden');
      } else {
        group.classList.add('hidden');
        document.getElementById('loan_duration').value = '';
      }
    });
  });

  document.getElementById('description').addEventListener('input', function() {
    document.getElementById('descCount').textContent = this.value.length;
  });

  document.querySelector('form').addEventListener('submit', function(e) {
    var desc = document.getElementById('description');
    var errors = [];

    if (desc.value.trim().length < 30) {
      errors.push('الوصف يجب أن يكون 30 حرفاً على الأقل.');
    }

    // Photo check
    var currentPhotos = <?= count($photos) ?>;
    var deletedPhotos = document.querySelectorAll('input[name="delete_photos[]"]:checked').length;
    var newPhotos = document.getElementById('photos').files.length;
    var totalPhotos = currentPhotos - deletedPhotos + newPhotos;

    if (totalPhotos < 1) {
      errors.push('يجب إبقاء صورة واحدة على الأقل للجهاز.');
    } else if (totalPhotos > 6) {
      errors.push('الحد الأقصى للصور الإجمالي هو 6 صور.');
    }

    var allowed = ['jpg', 'jpeg', 'png', 'webp'];
    var photosInput = document.getElementById('photos');
    for (var i = 0; i < photosInput.files.length; i++) {
      var parts = photosInput.files[i].name.split('.');
      var ext = parts[parts.length - 1].toLowerCase();
      if (allowed.indexOf(ext) === -1) {
        errors.push('الصورة رقم ' + (i + 1) + ' من نوع غير مسموح به.');
      }
    }

    if (errors.length > 0) {
      e.preventDefault();
      alert(errors.join('\n'));
    }
  });
</script>
<script src="assets/js/main.js"></script>
<script src="<?= LEAFLET_JS ?>"></script>
<script src="assets/js/maps.js"></script>
<script src="assets/js/validation.js"></script>
</body>
</html>
