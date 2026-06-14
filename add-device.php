<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$user = getCurrentUser();
if (!$user || !in_array($user['role'], ['donor', 'admin'])) {
    redirect('login.php?error=unauthorized');
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

        if (!isset($_FILES['photos']) || empty($_FILES['photos']['tmp_name'][0])) {
            $errors[] = 'يرجى إضافة صورة واحدة على الأقل.';
        } elseif (count($_FILES['photos']['tmp_name']) > 6) {
            $errors[] = 'الحد الأقصى للصور هو 6 صور.';
        }

        if (empty($errors)) {
            $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'devices' . DIRECTORY_SEPARATOR;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $uploadedPaths = [];
            $fileErrors = [];
            $files = $_FILES['photos'];
            $fileCount = count($files['tmp_name']);

            for ($i = 0; $i < $fileCount; $i++) {
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

            if (!empty($fileErrors)) {
                $error = implode('<br>', $fileErrors);
                foreach ($uploadedPaths as $path) {
                    @unlink(__DIR__ . DIRECTORY_SEPARATOR . $path);
                }
            } elseif (count($uploadedPaths) === 0) {
                $error = 'فشل رفع الصور. يرجى المحاولة مرة أخرى.';
            } else {
                try {
                    $pdo->beginTransaction();
                    $stmt = $pdo->prepare("INSERT INTO devices (donor_id, name, category, condition_rating, description, offer_type, loan_duration, governorate, district, latitude, longitude, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending_review')");
                    $stmt->execute([$user['id'], $name, $category, $conditionRating, $description, $offerType, $loanDuration ?: null, $governorate, $district, $latitude, $longitude]);
                    $deviceId = $pdo->lastInsertId();

                    $stmtPhoto = $pdo->prepare("INSERT INTO device_photos (device_id, file_path, is_primary) VALUES (?, ?, ?)");
                    foreach ($uploadedPaths as $idx => $path) {
                        $stmtPhoto->execute([$deviceId, $path, $idx === 0 ? 1 : 0]);
                    }

                    $pdo->commit();
                    $_SESSION['add_device_success'] = 'تم إضافة الجهاز بنجاح! سيتم مراجعته من قبل الإدارة. <a href="' . ($user['role'] === 'admin' ? 'admin/index.php' : 'dashboard-donor.php') . '" class="underline font-semibold">الذهاب إلى لوحة التحكم</a>';
                    redirect('add-device.php');
                } catch (Exception $e) {
                    $pdo->rollBack();
                    foreach ($uploadedPaths as $path) {
                        @unlink(__DIR__ . DIRECTORY_SEPARATOR . $path);
                    }
                    $error = 'حدث خطأ أثناء حفظ البيانات. يرجى المحاولة مرة أخرى.';
                }
            }
        } else {
            $error = implode('<br>', $errors);
        }
    }
}

if (isset($_SESSION['add_device_success'])) {
    $success = $_SESSION['add_device_success'];
    unset($_SESSION['add_device_success']);
}
?><!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>إضافة جهاز | سند</title>
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
      <h1 class="text-3xl font-bold text-primary">إضافة جهاز طبي</h1>
      <p class="text-text-muted mt-2">ساهم في دعم المحتاجين بتقديم جهاز طبي</p>
    </div>

    <form method="POST" action="" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

      <div class="mb-5">
        <label for="name" class="block text-sm font-semibold text-text-dark mb-1">اسم الجهاز</label>
        <input type="text" id="name" name="name" required maxlength="150" placeholder="أدخل اسم الجهاز الطبي" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
      </div>

      <div class="mb-5">
        <label for="category" class="block text-sm font-semibold text-text-dark mb-1">التصنيف الطبي</label>
        <select id="category" name="category" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white">
          <option value="">اختر التصنيف</option>
          <?php foreach ($categories as $key => $label): ?>
            <option value="<?= $key ?>"><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-5">
        <span class="block text-sm font-semibold text-text-dark mb-2">حالة الجهاز</span>
        <div class="grid grid-cols-3 gap-3">
          <?php foreach ($conditions as $key => $label): ?>
            <label class="flex items-center justify-center gap-2 p-3 rounded-xl border-2 border-gray-200 cursor-pointer transition-all has-[:checked]:border-primary has-[:checked]:bg-primary/5">
              <input type="radio" name="condition_rating" value="<?= $key ?>" class="appearance-none w-4 h-4 rounded-full border-2 border-gray-300 checked:border-primary checked:bg-primary checked:ring-2 checked:ring-primary/20 transition-all" required>
              <span class="text-sm font-medium"><?= $label ?></span>
            </label>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="mb-5">
        <label for="description" class="block text-sm font-semibold text-text-dark mb-1">الوصف</label>
        <textarea id="description" name="description" required minlength="30" maxlength="2000" rows="5" placeholder="اكتب وصفاً تفصيلياً للجهاز (30 حرفاً على الأقل)" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all resize-y"></textarea>
        <div class="flex justify-between mt-1 text-xs text-text-muted">
          <span id="descCount">0</span>
          <span>2000</span>
        </div>
      </div>

      <div class="mb-5">
        <span class="block text-sm font-semibold text-text-dark mb-2">نوع التبرع</span>
        <div class="grid grid-cols-2 gap-3">
          <?php foreach ($offerTypes as $key => $label): ?>
            <label class="flex items-center justify-center gap-2 p-3 rounded-xl border-2 border-gray-200 cursor-pointer transition-all has-[:checked]:border-primary has-[:checked]:bg-primary/5">
              <input type="radio" name="offer_type" value="<?= $key ?>" class="appearance-none w-4 h-4 rounded-full border-2 border-gray-300 checked:border-primary checked:bg-primary checked:ring-2 checked:ring-primary/20 transition-all" required>
              <span class="text-sm font-medium"><?= $label ?></span>
            </label>
          <?php endforeach; ?>
        </div>
      </div>

      <div id="loanDurationGroup" class="mb-5 hidden">
        <label for="loan_duration" class="block text-sm font-semibold text-text-dark mb-1">مدة الإعارة</label>
        <select id="loan_duration" name="loan_duration" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white">
          <option value="">اختر المدة</option>
          <?php foreach ($loanDurations as $key => $label): ?>
            <option value="<?= $key ?>"><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-5">
        <label for="governorate" class="block text-sm font-semibold text-text-dark mb-1">المحافظة</label>
        <select id="governorate" name="governorate" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white">
          <option value="">اختر المحافظة</option>
          <?php foreach ($govs as $key => $name): ?>
            <option value="<?= $key ?>"><?= $name ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-5">
        <label for="district" class="block text-sm font-semibold text-text-dark mb-1">المديرية</label>
        <select id="district" name="district" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white" disabled>
          <option value="">اختر المحافظة أولاً</option>
        </select>
      </div>

      <div class="mb-5">
        <span class="block text-sm font-semibold text-text-dark mb-1">الموقع على الخريطة</span>
        <div id="mapPicker" class="w-full rounded-xl bg-gray-100" style="height: 300px;"></div>
        <input type="hidden" name="latitude" id="latitude" value="">
        <input type="hidden" name="longitude" id="longitude" value="">
        <p class="text-xs text-text-muted mt-1">يمكنك تحديد الموقع على الخريطة (اختياري)</p>
      </div>

      <div class="mb-6">
        <label for="photos" class="block text-sm font-semibold text-text-dark mb-1">إضافة صور</label>
        <input type="file" id="photos" name="photos[]" multiple accept=".jpg,.jpeg,.png,.webp" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white file:ml-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary file:text-white file:font-semibold file:cursor-pointer file:hover:bg-primary-dark">
        <div id="photoPreview" class="mt-4"></div>
        <p class="text-xs text-text-muted mt-1">يمكنك إضافة حتى 6 صور. الصورة الأولى ستكون الصورة الرئيسية.</p>
      </div>

      <button type="submit" class="bg-primary hover:bg-primary-dark text-white w-full py-3 rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl text-lg">إضافة الجهاز</button>
    </form>

    <p class="text-center text-text-muted text-sm mt-6">
      <a href="marketplace.php" class="text-primary hover:text-primary-dark font-semibold">العودة إلى السوق</a>
    </p>
  </div>
</div>

<script>
  window.districts = <?= json_encode($districtsData, JSON_UNESCAPED_UNICODE) ?>;

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
      distSelect.appendChild(opt);
    });
  }

  document.getElementById('governorate').addEventListener('change', populateDistricts);

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
    var photos = document.getElementById('photos');
    var desc = document.getElementById('description');
    var errors = [];

    if (desc.value.trim().length < 30) {
      errors.push('الوصف يجب أن يكون 30 حرفاً على الأقل.');
    }

    if (photos.files.length === 0) {
      errors.push('يرجى إضافة صورة واحدة على الأقل.');
    } else if (photos.files.length > 6) {
      errors.push('الحد الأقصى للصور هو 6 صور.');
    } else {
      var allowed = ['jpg', 'jpeg', 'png', 'webp'];
      for (var i = 0; i < photos.files.length; i++) {
        var parts = photos.files[i].name.split('.');
        var ext = parts[parts.length - 1].toLowerCase();
        if (allowed.indexOf(ext) === -1) {
          errors.push('الصورة رقم ' + (i + 1) + ' من نوع غير مسموح به.');
        }
      }
    }

    if (errors.length > 0) {
      e.preventDefault();
      alert(errors.join('\n'));
    }
  });
</script>
<script src="assets/js/main.js"></script>
<script>var GOOGLE_MAPS_API_KEY = '<?= GOOGLE_MAPS_API_KEY ?>';</script>
<script src="assets/js/maps.js"></script>
<script src="assets/js/validation.js"></script>
</body>
</html>
