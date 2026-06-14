<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

$currentUser = getCurrentUser();
if (!$currentUser || $currentUser['role'] !== 'admin') {
    jsonResponse(['error' => 'غير مصرح'], 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../login.php?error=unauthorized');
}

if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    jsonResponse(['error' => 'رمز CSRF غير صالح'], 422);
}

$action = $_POST['action'] ?? '';

$allowedActions = ['approve_device', 'reject_device', 'approve_request', 'reject_request'];
if (!in_array($action, $allowedActions)) {
    redirect('../login.php?error=unauthorized');
}

$isDeviceAction = in_array($action, ['approve_device', 'reject_device']);

if ($isDeviceAction) {
    $entityId = isset($_POST['device_id']) ? (int)$_POST['device_id'] : 0;
    $redirectPage = 'listings.php';
} else {
    $entityId = isset($_POST['request_id']) ? (int)$_POST['request_id'] : 0;
    $redirectPage = 'requests.php';
}

if ($entityId <= 0) {
    redirect($redirectPage . '?error=' . urlencode('معرف غير صالح'));
}

$rejectionReason = '';
$isRejectAction = in_array($action, ['reject_device', 'reject_request']);
if ($isRejectAction) {
    $rejectionReason = trim($_POST['rejection_reason'] ?? '');
    if (mb_strlen($rejectionReason) < 10) {
        redirect($redirectPage . '?error=' . urlencode('سبب الرفض يجب أن يكون 10 أحرف على الأقل'));
    }
}

$adminId = (int)$currentUser['id'];

try {
    $pdo->beginTransaction();

    if ($action === 'approve_device') {
        $stmt = $pdo->prepare("UPDATE devices SET status='active', admin_reviewed_by=?, admin_reviewed_at=NOW() WHERE id=?");
        $stmt->execute([$adminId, $entityId]);
    } elseif ($action === 'reject_device') {
        $stmt = $pdo->prepare("UPDATE devices SET status='rejected', rejection_reason=?, admin_reviewed_by=?, admin_reviewed_at=NOW() WHERE id=?");
        $stmt->execute([$rejectionReason, $adminId, $entityId]);
    } elseif ($action === 'approve_request') {
        $stmt = $pdo->prepare("UPDATE requests SET status='approved', admin_reviewed_by=?, admin_reviewed_at=NOW() WHERE id=?");
        $stmt->execute([$adminId, $entityId]);

        $stmt = $pdo->prepare("UPDATE devices SET status='loaned', admin_reviewed_by=?, admin_reviewed_at=NOW() WHERE id=(SELECT device_id FROM requests WHERE id=?)");
        $stmt->execute([$adminId, $entityId]);
    } elseif ($action === 'reject_request') {
        $stmt = $pdo->prepare("UPDATE requests SET status='rejected', rejection_reason=?, admin_reviewed_by=?, admin_reviewed_at=NOW() WHERE id=?");
        $stmt->execute([$rejectionReason, $adminId, $entityId]);

        $stmt = $pdo->prepare("UPDATE devices SET status='active' WHERE id=(SELECT device_id FROM requests WHERE id=?)");
        $stmt->execute([$entityId]);
    }

    $pdo->commit();

    if ($action === 'approve_request') {
        $stmt = $pdo->prepare("SELECT d.name AS device_name, d.donor_id, r.beneficiary_id,
                                      u_donor.phone AS donor_phone, u_ben.full_name AS beneficiary_name
                               FROM requests r
                               JOIN devices d ON r.device_id = d.id
                               JOIN users u_donor ON d.donor_id = u_donor.id
                               JOIN users u_ben ON r.beneficiary_id = u_ben.id
                               WHERE r.id = ?");
        $stmt->execute([$entityId]);
        $info = $stmt->fetch();

        if ($info) {
            $formattedPhone = formatYemeniPhone($info['donor_phone']);
            $deviceName = htmlspecialchars($info['device_name']);
            $beneficiaryName = htmlspecialchars($info['beneficiary_name']);
            $message = "السلام عليكم، أنا {$beneficiaryName}، تواصلت معكم بخصوص جهاز {$deviceName} حسب إعلانكم في منصة سند. أرجو الإفادة عن كيفية الاستلام.";
            $whatsappUrl = generateWhatsAppUrl($formattedPhone, $message);
            $_SESSION['flash_contact'] = [
                'donor_phone' => $info['donor_phone'],
                'whatsapp_url' => $whatsappUrl,
                'tel_url' => 'tel:+' . $formattedPhone,
            ];
        }
    }

    if ($isDeviceAction) {
        redirect('listings.php?msg=' . urlencode('تم تحديث حالة الجهاز بنجاح'));
    } else {
        redirect('requests.php?msg=' . urlencode('تم تحديث حالة الطلب بنجاح'));
    }
} catch (Exception $e) {
    $pdo->rollBack();
    redirect($redirectPage . '?error=' . urlencode('حدث خطأ أثناء تحديث الحالة'));
}
