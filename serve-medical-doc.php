<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

requireRole('admin');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(404);
    exit;
}

$stmt = $pdo->prepare("SELECT medical_doc_path FROM requests WHERE id = ?");
$stmt->execute([$id]);
$request = $stmt->fetch();

if (!$request || empty($request['medical_doc_path'])) {
    http_response_code(404);
    exit;
}

$filePath = __DIR__ . '/' . $request['medical_doc_path'];

if (!file_exists($filePath)) {
    http_response_code(404);
    exit;
}

$ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

$mimeMap = [
    'pdf'  => 'application/pdf',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png'  => 'image/png',
    'webp' => 'image/webp',
];

$contentType = $mimeMap[$ext] ?? 'application/octet-stream';

$filename = basename($filePath);

header('Content-Type: ' . $contentType);
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Content-Length: ' . filesize($filePath));

readfile($filePath);
exit;
