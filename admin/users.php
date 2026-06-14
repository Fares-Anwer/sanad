<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin', '../login.php?error=unauthorized');
$currentUser = getCurrentUser();
$csrf = generateCSRFToken();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_action'])) {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!validateCSRFToken($csrfToken)) {
        $message = 'رمز الحماية غير صالح';
    } else {
        $userId = (int) ($_POST['user_id'] ?? 0);
        $action = $_POST['toggle_action'];

        if ($userId === (int) $currentUser['id']) {
            $message = 'لا يمكنك إيقاف حسابك الخاص';
        } elseif ($action === 'deactivate') {
            $stmt = $pdo->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
            $stmt->execute([$userId]);
            $message = 'تم إيقاف المستخدم بنجاح';
        } elseif ($action === 'activate') {
            $stmt = $pdo->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
            $stmt->execute([$userId]);
            $message = 'تم تفعيل المستخدم بنجاح';
        }
    }
}

$roleFilter = $_GET['role'] ?? '';
$govFilter = $_GET['governorate'] ?? '';

$sql = "SELECT * FROM users";
$params = [];

if ($roleFilter !== '' && in_array($roleFilter, ['beneficiary', 'donor', 'admin'])) {
    $sql .= " WHERE role = ?";
    $params[] = $roleFilter;
    if ($govFilter !== '' && array_key_exists($govFilter, getYemenGovernorates())) {
        $sql .= " AND governorate = ?";
        $params[] = $govFilter;
    }
} elseif ($govFilter !== '' && array_key_exists($govFilter, getYemenGovernorates())) {
    $sql .= " WHERE governorate = ?";
    $params[] = $govFilter;
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

$governorates = getYemenGovernorates();

$roleLabels = [
    'beneficiary' => 'مستفيد',
    'donor' => 'متبرع',
    'admin' => 'إدارة',
];
?><!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>إدارة المستخدمين | سند</title>
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
    <a href="../index.php" class="text-2xl font-bold text-primary">سند</a>
    <div class="flex gap-4 items-center">
      <a href="index.php" class="text-text-muted hover:text-primary transition">الرئيسية</a>
      <a href="listings.php" class="text-text-muted hover:text-primary transition">الأجهزة</a>
      <a href="requests.php" class="text-text-muted hover:text-primary transition">الطلبات</a>
      <a href="users.php" class="text-primary font-semibold transition">المستخدمين</a>
      <a href="../marketplace.php" class="text-text-muted hover:text-primary transition">السوق</a>
      <a href="../logout.php" class="text-red-500 hover:text-red-700 transition">تسجيل الخروج</a>
    </div>
  </div>
</nav>

<div class="max-w-6xl mx-auto p-6 fade-in">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">إدارة المستخدمين</h1>
  </div>

  <?php if ($message): ?>
    <div class="glass rounded-xl p-4 mb-6 text-center text-lg font-semibold text-primary-dark"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <div class="glass rounded-2xl p-5 mb-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="block text-text-muted text-sm mb-1">الدور</label>
        <select name="role" class="border border-gray-200 rounded-xl px-4 py-2 bg-white focus:outline-none focus:border-primary">
          <option value="">الكل</option>
          <option value="beneficiary" <?= $roleFilter === 'beneficiary' ? 'selected' : '' ?>>مستفيد</option>
          <option value="donor" <?= $roleFilter === 'donor' ? 'selected' : '' ?>>متبرع</option>
          <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>إدارة</option>
        </select>
      </div>
      <div>
        <label class="block text-text-muted text-sm mb-1">المحافظة</label>
        <select name="governorate" class="border border-gray-200 rounded-xl px-4 py-2 bg-white focus:outline-none focus:border-primary">
          <option value="">الكل</option>
          <?php foreach ($governorates as $key => $name): ?>
            <option value="<?= $key ?>" <?= $govFilter === $key ? 'selected' : '' ?>><?= $name ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-6 py-2 rounded-xl font-semibold transition">تصفية</button>
      <a href="users.php" class="text-text-muted hover:text-primary transition px-4 py-2">إعادة تعيين</a>
    </form>
  </div>

  <div class="glass rounded-2xl p-6 overflow-x-auto">
    <?php if (empty($users)): ?>
      <div class="text-center py-12">
        <p class="text-text-muted text-lg">لا يوجد مستخدمين</p>
      </div>
    <?php else: ?>
      <table class="w-full text-right">
        <thead>
          <tr class="border-b border-gray-200">
            <th class="pb-3 font-semibold text-text-muted">الاسم</th>
            <th class="pb-3 font-semibold text-text-muted">البريد الإلكتروني</th>
            <th class="pb-3 font-semibold text-text-muted">رقم الجوال</th>
            <th class="pb-3 font-semibold text-text-muted">الدور</th>
            <th class="pb-3 font-semibold text-text-muted">المحافظة</th>
            <th class="pb-3 font-semibold text-text-muted">تاريخ التسجيل</th>
            <th class="pb-3 font-semibold text-text-muted">الحالة</th>
            <th class="pb-3 font-semibold text-text-muted">إجراءات</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
            <tr class="border-b border-gray-100 hover:bg-white/50 transition">
              <td class="py-3 font-medium"><?= htmlspecialchars($u['full_name']) ?></td>
              <td class="py-3 text-text-muted"><?= htmlspecialchars($u['email']) ?></td>
              <td class="py-3"><?= htmlspecialchars($u['phone']) ?></td>
              <td class="py-3"><?= $roleLabels[$u['role']] ?? $u['role'] ?></td>
              <td class="py-3"><?= $governorates[$u['governorate']] ?? $u['governorate'] ?></td>
              <td class="py-3 text-text-muted text-sm"><?= date('Y/m/d', strtotime($u['created_at'])) ?></td>
              <td class="py-3">
                <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $u['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                  <?= $u['is_active'] ? 'نشط' : 'موقوف' ?>
                </span>
              </td>
              <td class="py-3">
                <form method="POST" style="display:inline">
                  <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                  <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                  <input type="hidden" name="toggle_action" value="<?= $u['is_active'] ? 'deactivate' : 'activate' ?>">
                  <button type="submit" class="<?= $u['is_active'] ? 'bg-red-500' : 'bg-green-500' ?> text-white px-3 py-1 rounded text-sm" <?= ($u['id'] == $currentUser['id']) ? 'disabled' : '' ?>><?= $u['is_active'] ? 'إيقاف' : 'تفعيل' ?></button>
                </form>
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
