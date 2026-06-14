<?php
require_once __DIR__ . '/functions.php';

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function getCurrentUser(): ?array {
    if (!isLoggedIn()) {
        return null;
    }
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND is_active = 1");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    return $user ?: null;
}

function requireRole(string $role, string $redirectUrl = 'login.php?error=unauthorized'): void {
    if (!isLoggedIn()) {
        redirect($redirectUrl);
    }
    $user = getCurrentUser();
    if (!$user || $user['role'] !== $role) {
        redirect($redirectUrl);
    }
}

function loginUser(string $email, string $password): array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        return ['success' => false, 'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'];
    }

    if (!$user['is_active']) {
        return ['success' => false, 'message' => 'الحساب غير نشط. يرجى التواصل مع الإدارة'];
    }

    if (!password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'];
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_name'] = $user['full_name'];

    return ['success' => true, 'role' => $user['role']];
}

function logoutUser(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
