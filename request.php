<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'طريقة الطلب غير صحيحة'], 405);
}

if (!isLoggedIn()) {
    jsonResponse(['error' => 'يجب تسجيل الدخول أولاً'], 401);
}

$user = getCurrentUser();
if (!$user || $user['role'] !== 'beneficiary') {
    jsonResponse(['error' => 'غير مصرح لك بتنفيذ هذا الإجراء'], 403);
}

if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    jsonResponse(['error' => 'رمز غير صالح. حاول مرة أخرى.'], 422);
}

$deviceId = isset($_POST['device_id']) ? (int)$_POST['device_id'] : 0;
if ($deviceId <= 0) {
    jsonResponse(['error' => 'معرف الجهاز غير صحيح'], 422);
}

$stmt = $pdo->prepare("SELECT * FROM devices WHERE id = ?");
$stmt->execute([$deviceId]);
$device = $stmt->fetch();

if (!$device) {
    jsonResponse(['error' => 'الجهاز غير موجود'], 404);
}

if ($device['status'] !== 'active') {
    if (in_array($device['status'], ['under_request_review', 'loaned'])) {
        jsonResponse(['error' => 'الجهاز قيد الطلب حالياً'], 422);
    }
    jsonResponse(['error' => 'الجهاز غير متاح حالياً'], 422);
}

$stmt = $pdo->prepare("SELECT id FROM requests WHERE device_id = ? AND status IN ('pending', 'approved') LIMIT 1");
$stmt->execute([$deviceId]);
if ($stmt->fetch()) {
    jsonResponse(['error' => 'يوجد طلب قيد المراجعة لهذا الجهاز'], 422);
}

$caseDescription = sanitizeTextarea($_POST['case_description'] ?? '');
$descLen = mb_strlen($caseDescription);
if ($descLen < 50 || $descLen > 2000) {
    jsonResponse(['error' => 'الوصف يجب أن يكون بين 50 و 2000 حرف'], 422);
}

if (!isset($_FILES['medical_doc']) || $_FILES['medical_doc']['error'] !== UPLOAD_ERR_OK) {
    jsonResponse(['error' => 'يرجى رفع تقرير طبي'], 422);
}

$file = $_FILES['medical_doc'];

if ($file['size'] > UPLOAD_MAX_SIZE) {
    jsonResponse(['error' => 'حجم الملف يتجاوز 5 ميجابايت'], 422);
}

if (!isAllowedExtension($file['name'])) {
    jsonResponse(['error' => 'نوع الملف غير مسموح به'], 422);
}

if (!validateFileMIME($file['tmp_name'])) {
    jsonResponse(['error' => 'نوع الملف غير مسموح به'], 422);
}

$uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'medical-reports' . DIRECTORY_SEPARATOR;
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$ext = getFileExtension($file['name']);
$newName = generateUUID() . '.' . $ext;
$destPath = $uploadDir . $newName;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    jsonResponse(['error' => 'فشل في رفع الملف'], 500);
}

$medicalDocPath = 'uploads/medical-reports/' . $newName;

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO requests (device_id, beneficiary_id, case_description, medical_doc_path, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt->execute([$deviceId, $user['id'], $caseDescription, $medicalDocPath]);

    $stmt = $pdo->prepare("UPDATE devices SET status = 'under_request_review' WHERE id = ?");
    $stmt->execute([$deviceId]);

    $pdo->commit();
    jsonResponse(['success' => true, 'message' => 'تم إرسال طلبك بنجاح']);
} catch (Exception $e) {
    $pdo->rollBack();
    @unlink($destPath);
    jsonResponse(['error' => 'حدث خطأ، يرجى المحاولة مرة أخرى'], 500);
}
