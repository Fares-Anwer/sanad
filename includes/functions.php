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

function getYemenGovernorates(): array {
    return [
        'amanat_al_asimah' => 'أمانة العاصمة',
        'aden'             => 'عدن',
        'taiz'             => 'تعز',
        'al_hudaydah'      => 'الحديدة',
        'hadramawt'        => 'حضرموت',
        'dhamar'           => 'ذمار',
        'shabwah'          => 'شبوة',
        'al_mahwit'        => 'المحويت',
        'saada'            => 'صعدة',
        'hajjah'           => 'حجة',
        'ibb'              => 'إب',
        'lahij'            => 'لحج',
        'marib'            => 'مأرب',
        'ad_dali'          => 'الضالع',
        'amran'            => 'عمران',
        'al_bayda'         => 'البيضاء',
        'al_mahrah'        => 'المهرة',
        'raymah'           => 'ريمة',
        'al_jawf'          => 'الجوف',
        'socotra'          => 'سقطرى',
        'abyan'            => 'أبين',
    ];
}

function getDistricts(string $governorateKey): array {
    switch ($governorateKey) {
        case 'amanat_al_asimah': return ['الصافية', 'التحرير', 'الثورة', 'شعوب'];
        case 'aden':             return ['خور مكسر', 'المعلا', 'كريتر', 'المنصورة'];
        case 'taiz':             return ['المظفر', 'القاهرة', 'المعافر', 'صبر الموادم'];
        case 'al_hudaydah':      return ['الحالي', 'الميناء', 'التحيتا', 'الخوخة'];
        case 'hadramawt':        return ['السيئون', 'المكلا', 'تريم', 'القطن'];
        case 'dhamar':           return ['مدينة ذمار', 'عتمة', 'ميفعة عنس', 'ضوران'];
        case 'shabwah':          return ['عتق', 'ميفعة', 'عرماء', 'حبان'];
        case 'al_mahwit':        return ['مدينة المحويت', 'الخبت', 'حفاش', 'بني سعد'];
        case 'saada':            return ['مدينة صعدة', 'حيدان', 'سحار', 'ظاهر'];
        case 'hajjah':           return ['مدينة حجة', 'بني قيس', 'أفلح', 'الجمعة'];
        case 'ibb':              return ['مدينة إب', 'السياني', 'الرضمة', 'يريم'];
        case 'lahij':            return ['الحوطة', 'تبن', 'المقاطرة', 'المضاربة'];
        case 'marib':            return ['مدينة مأرب', 'الوادي', 'حريب', 'الجوبة'];
        case 'ad_dali':          return ['مدينة الضالع', 'قعطبة', 'الحشاء', 'الشعيب'];
        case 'amran':            return ['مدينة عمران', 'ذيبين', 'شهارة', 'حوث'];
        case 'al_bayda':         return ['مدينة البيضاء', 'البيضاء', 'ذي ناعم', 'ناطع'];
        case 'al_mahrah':        return ['الغيضة', 'حوف', 'شحن', 'المسيلة'];
        case 'raymah':           return ['مدينة ريمة', 'الجعفرية', 'بلاد الطعام', 'مور'];
        case 'al_jawf':          return ['الحزم', 'المطمة', 'خب والشعف', 'الغيل'];
        case 'socotra':          return ['حديبو', 'قلنسية', 'عبد الكوري'];
        case 'abyan':            return ['زنجبار', 'خنفر', 'الوضيع', 'مودية'];
        default:                 return [];
    }
}
