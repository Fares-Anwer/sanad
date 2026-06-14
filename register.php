<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$csrf = generateCSRFToken();
$error = '';
$formData = [
    'full_name'   => '',
    'phone'       => '',
    'email'       => '',
    'role'        => '',
    'governorate' => '',
    'district'    => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'رمز غير صالح. حاول مرة أخرى.';
    } else {
        $formData['full_name']   = sanitizeInput($_POST['full_name'] ?? '');
        $formData['phone']       = sanitizeInput($_POST['phone'] ?? '');
        $formData['email']       = sanitizeInput($_POST['email'] ?? '');
        $formData['role']        = $_POST['role'] ?? '';
        $formData['governorate'] = $_POST['governorate'] ?? '';
        $formData['district']    = sanitizeInput($_POST['district'] ?? '');
        $password                = $_POST['password'] ?? '';
        $confirm_password        = $_POST['confirm_password'] ?? '';

        $errors = [];

        $nameLen = mb_strlen($formData['full_name']);
        if ($nameLen < 3 || $nameLen > 100) {
            $errors[] = 'الاسم يجب أن يكون بين 3 و 100 حرف.';
        }

        $formattedPhone = formatYemeniPhone($formData['phone']);
        if (strlen($formattedPhone) !== 12) {
            $errors[] = 'رقم الجوال غير صحيح. يجب أن يكون رقم يمني صحيح.';
        }

        if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'البريد الإلكتروني غير صحيح.';
        } else {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
            $stmt->execute([$formData['email']]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = 'البريد الإلكتروني مستخدم بالفعل.';
            }
        }

        if (strlen($password) < 8 || !preg_match('/[0-9]/', $password)) {
            $errors[] = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل وتحتوي على رقم واحد على الأقل.';
        }

        if ($password !== $confirm_password) {
            $errors[] = 'كلمة المرور غير متطابقة.';
        }

        $governorates = getYemenGovernorates();
        if (!array_key_exists($formData['governorate'], $governorates)) {
            $errors[] = 'يرجى اختيار المحافظة.';
        }

        $districts = getDistricts($formData['governorate']);
        if (!in_array($formData['district'], $districts)) {
            $errors[] = 'يرجى اختيار المديرية.';
        }

        if (!in_array($formData['role'], ['donor', 'beneficiary'])) {
            $errors[] = 'يرجى اختيار نوع الحساب.';
        }

        if (empty($errors)) {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('INSERT INTO users (full_name, phone, email, password_hash, role, governorate, district) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$formData['full_name'], $formattedPhone, $formData['email'], $password_hash, $formData['role'], $formData['governorate'], $formData['district']]);
            $_SESSION['register_success'] = true;
            redirect('login.php');
        } else {
            $error = implode('<br>', $errors);
        }
    }
}

$allGovs = getYemenGovernorates();
$districtsData = [];
foreach ($allGovs as $key => $name) {
    $districtsData[$key] = getDistricts($key);
}
?><!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>إنشاء حساب | سند</title>
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
  <div class="min-h-screen flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-lg">
      <?php if (isset($_SESSION['register_success'])): unset($_SESSION['register_success']); ?>
        <div class="bg-green-50 text-green-700 p-4 rounded-xl text-center mb-6 fade-in border border-green-200">
          تم إنشاء الحساب بنجاح! يمكنك الآن تسجيل الدخول.
        </div>
      <?php endif; ?>

      <div class="glass rounded-2xl p-8 shadow-xl fade-in">
        <div class="text-center mb-6">
          <h1 class="text-3xl font-bold text-primary">إنشاء حساب جديد</h1>
          <p class="text-text-muted mt-2">انضم إلى منصة سند للتكافل الطبي</p>
        </div>

        <?php if ($error): ?>
          <div class="bg-red-50 text-red-600 p-3 rounded-xl text-sm mb-4 border border-red-200"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="" novalidate>
          <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

          <div class="mb-4">
            <label for="full_name" class="block text-sm font-semibold text-text-dark mb-1">الاسم الكامل</label>
            <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($formData['full_name']) ?>" required minlength="3" maxlength="100" placeholder="أدخل اسمك الكامل" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
          </div>

          <div class="mb-4">
            <label for="phone" class="block text-sm font-semibold text-text-dark mb-1">رقم الجوال</label>
            <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($formData['phone']) ?>" required placeholder="مثال: 777123456" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
          </div>

          <div class="mb-4">
            <label for="email" class="block text-sm font-semibold text-text-dark mb-1">البريد الإلكتروني</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($formData['email']) ?>" required placeholder="example@email.com" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
          </div>

          <div class="mb-4">
            <label for="password" class="block text-sm font-semibold text-text-dark mb-1">كلمة المرور</label>
            <input type="password" id="password" name="password" required minlength="8" placeholder="8 أحرف على الأقل + رقم" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
          </div>

          <div class="mb-4">
            <label for="confirm_password" class="block text-sm font-semibold text-text-dark mb-1">تأكيد كلمة المرور</label>
            <input type="password" id="confirm_password" name="confirm_password" required placeholder="أعد إدخال كلمة المرور" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
          </div>

          <div class="mb-4">
            <span class="block text-sm font-semibold text-text-dark mb-2">نوع الحساب</span>
            <div class="flex gap-4">
              <label class="flex-1 flex items-center justify-center gap-2 p-3 rounded-xl border-2 border-gray-200 cursor-pointer transition-all has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                <input type="radio" name="role" value="donor" class="appearance-none w-4 h-4 rounded-full border-2 border-gray-300 checked:border-primary checked:bg-primary checked:ring-2 checked:ring-primary/20 transition-all" <?= $formData['role'] === 'donor' ? 'checked' : '' ?> required>
                <span class="text-sm font-medium">متبرع</span>
              </label>
              <label class="flex-1 flex items-center justify-center gap-2 p-3 rounded-xl border-2 border-gray-200 cursor-pointer transition-all has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                <input type="radio" name="role" value="beneficiary" class="appearance-none w-4 h-4 rounded-full border-2 border-gray-300 checked:border-primary checked:bg-primary checked:ring-2 checked:ring-primary/20 transition-all" <?= $formData['role'] === 'beneficiary' ? 'checked' : '' ?> required>
                <span class="text-sm font-medium">مستفيد</span>
              </label>
            </div>
          </div>

          <div class="mb-4">
            <label for="governorate" class="block text-sm font-semibold text-text-dark mb-1">المحافظة</label>
            <select id="governorate" name="governorate" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white">
              <option value="">اختر المحافظة</option>
              <?php foreach ($allGovs as $key => $name): ?>
                <option value="<?= $key ?>" <?= $formData['governorate'] === $key ? 'selected' : '' ?>><?= $name ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-6">
            <label for="district" class="block text-sm font-semibold text-text-dark mb-1">المديرية</label>
            <select id="district" name="district" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white" disabled>
              <option value="">اختر المحافظة أولاً</option>
            </select>
          </div>

          <button type="submit" class="bg-primary hover:bg-primary-dark text-white w-full py-3 rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl">إنشاء الحساب</button>
        </form>

        <p class="text-center text-text-muted text-sm mt-6">
          لديك حساب بالفعل؟
          <a href="login.php" class="text-primary hover:text-primary-dark font-semibold">تسجيل الدخول</a>
        </p>
      </div>
    </div>
  </div>

  <script>
    window.districts = <?= json_encode($districtsData, JSON_UNESCAPED_UNICODE) ?>;

    function populateDistricts() {
      const govSelect = document.getElementById('governorate');
      const distSelect = document.getElementById('district');
      const selectedGov = govSelect.value;

      distSelect.innerHTML = '';
      distSelect.disabled = true;

      if (!selectedGov || !window.districts[selectedGov]) {
        distSelect.innerHTML = '<option value="">اختر المحافظة أولاً</option>';
        return;
      }

      distSelect.disabled = false;
      distSelect.innerHTML = '<option value="">اختر المديرية</option>';

      window.districts[selectedGov].forEach(function(district) {
        const opt = document.createElement('option');
        opt.value = district;
        opt.textContent = district;
        distSelect.appendChild(opt);
      });
    }

    document.getElementById('governorate').addEventListener('change', populateDistricts);

    <?php if (!empty($formData['governorate'])): ?>
    document.addEventListener('DOMContentLoaded', function() {
      populateDistricts();
      document.getElementById('district').value = '<?= htmlspecialchars($formData['district'], ENT_QUOTES) ?>';
    });
    <?php endif; ?>
  </script>
</body>
</html>
