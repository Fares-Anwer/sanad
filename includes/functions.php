<?php
require_once __DIR__ . '/config.php';

function generateUUID(): string {
    $data = random_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function sanitizeInput(string $input): string {
    return htmlspecialchars(trim(strip_tags($input)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function sanitizeTextarea(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function generateCSRFToken(): string {
    if (empty($_SESSION['csrf_token']) || (isset($_SESSION['csrf_token_time']) && (time() - $_SESSION['csrf_token_time']) > CSRF_TOKEN_LIFETIME)) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken(string $token): bool {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    if ((time() - $_SESSION['csrf_token_time']) > CSRF_TOKEN_LIFETIME) {
        unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function validateFileMIME(string $filePath): bool {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $filePath);
    finfo_close($finfo);
    return in_array($mime, ALLOWED_MIMES);
}

function getFileExtension(string $filename): string {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

function isAllowedExtension(string $filename): bool {
    return in_array(getFileExtension($filename), ALLOWED_EXTENSIONS);
}

function formatYemeniPhone(string $phone): string {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    $phone = ltrim($phone, '0');
    if (substr($phone, 0, 3) !== '967') {
        $phone = '967' . $phone;
    }
    return $phone;
}

function generateWhatsAppUrl(string $phone, string $message): string {
    $phone = formatYemeniPhone($phone);
    $message = rawurlencode($message);
    return "https://wa.me/{$phone}?text={$message}";
}

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

function jsonResponse(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
