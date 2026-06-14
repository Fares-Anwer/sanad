<?php
/**
 * Sanad Platform — Placeholder Device Images & Medical Report PDFs
 * Generates valid JPEG images via GD library and simple PDF stubs.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

$basePath = __DIR__ . '/uploads';

// ── 1. Ensure directories exist ──────────────────────────────────────
$dirs = [
    $basePath . '/devices',
    $basePath . '/medical-reports',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "[+] Created directory: $dir\n";
    } else {
        echo "[=] Directory exists:  $dir\n";
    }
}

// ── 2. Font detection ────────────────────────────────────────────────
// Try to find a TTF font that supports Arabic on this Windows system.
$fontCandidates = [
    'C:/Windows/Fonts/tahoma.ttf',
    'C:/Windows/Fonts/tahomabd.ttf',
    'C:/Windows/Fonts/arial.ttf',
    'C:/Windows/Fonts/arialbd.ttf',
    'C:/Windows/Fonts/segoeui.ttf',
];
$fontPath = null;
foreach ($fontCandidates as $f) {
    if (file_exists($f)) {
        $fontPath = $f;
        break;
    }
}
if ($fontPath === null) {
    echo "[!] WARNING: No TTF font found. Labels will be missing.\n";
}

// ── 3. Device seed data ──────────────────────────────────────────────
// id => [arabic_name, category]
$devices = [
    1  => ['جهاز أكسجين مركزي',       'respiratory'],
    2  => ['كرسي متحرك',               'mobility'],
    3  => ['سرير مستشفي كهربائي',      'beds_clinical'],
    4  => ['جهاز قياس ضغط الدم',       'diagnostic'],
    5  => ['أسطوانة أكسجين',           'respiratory'],
    6  => ['عكازين مشي',               'mobility'],
    7  => ['سرير تدبير ضغط',           'beds_clinical'],
    8  => ['جهاز قياس نبض',            'diagnostic'],
    9  => ['جهاز استنشاق',             'respiratory'],
    10 => ['جهاز مشي',                 'mobility'],
    11 => ['حامل المغذيات',            'beds_clinical'],
    12 => ['جهاز قياس سكر',            'diagnostic'],
    13 => ['جهاز CPAP',                'respiratory'],
    14 => ['رافع المريض',              'beds_clinical'],
    15 => ['ميزان حرارة رقمي',         'diagnostic'],
];

$categoryColors = [
    'respiratory'    => [0, 180, 216],   // #00B4D8  blue
    'mobility'       => [46, 204, 113],  // #2ECC71  green
    'beds_clinical'  => [230, 126, 34],  // #E67E22  orange
    'diagnostic'     => [155, 89, 182],  // #9B59B6  purple
];

// Simple medical icons drawn per category (geometric shapes)
function drawRespiratoryIcon($img, $cx, $cy, $size) {
    // Lungs: two overlapping circles
    $r = (int)round($size * 0.35);
    $off = (int)round($size * 0.22);
    $white = imagecolorallocate($img, 255, 255, 255);
    imagefilledellipse($img, $cx - $off, $cy, $r * 2, $r * 2, $white);
    imagefilledellipse($img, $cx + $off, $cy, $r * 2, $r * 2, $white);
    // Trachea line
    imageline($img, $cx, $cy - $r - 10, $cx, $cy, $white);
    imagesetthickness($img, 3);
    imageline($img, $cx, $cy - $r - 10, $cx, $cy, $white);
    imagesetthickness($img, 1);
}

function drawMobilityIcon($img, $cx, $cy, $size) {
    // Wheelchair: circle + lines
    $white = imagecolorallocate($img, 255, 255, 255);
    $r = (int)round($size * 0.35);
    imagesetthickness($img, 3);
    imagearc($img, $cx, $cy + 10, $r * 2, $r * 2, 0, 360, $white);
    // Seat + backrest
    imageline($img, $cx - $r + 5, $cy - 15, $cx + $r - 10, $cy - 15, $white);
    imageline($img, $cx - $r + 5, $cy - 15, $cx - $r + 5, $cy - $r + 5, $white);
    // Footrest
    imageline($img, $cx + $r - 10, $cy - 15, $cx + $r + 5, $cy + 15, $white);
    imagesetthickness($img, 1);
}

function drawBedsClinicalIcon($img, $cx, $cy, $size) {
    // Bed: rectangle + legs
    $white = imagecolorallocate($img, 255, 255, 255);
    $w = (int)round($size * 0.8);
    $h = (int)round($size * 0.15);
    $y = (int)round($cy - $h / 2);
    imagefilledrectangle($img, (int)round($cx - $w / 2), $y, (int)round($cx + $w / 2), $y + $h, $white);
    // Headboard
    imagefilledrectangle($img, (int)round($cx - $w / 2 - 5), (int)round($y - $h * 2), (int)round($cx - $w / 2 + 5), $y + $h, $white);
    // Legs
    $legColor = imagecolorallocate($img, 255, 255, 255);
    imageline($img, (int)round($cx - $w / 2 + 3), $y + $h, (int)round($cx - $w / 2 + 3), $y + $h + 15, $legColor);
    imageline($img, (int)round($cx + $w / 2 - 3), $y + $h, (int)round($cx + $w / 2 - 3), $y + $h + 15, $legColor);
}

function drawDiagnosticIcon($img, $cx, $cy, $size) {
    // Heartbeat / monitor line
    $white = imagecolorallocate($img, 255, 255, 255);
    imagesetthickness($img, 3);
    $points = [
        [$cx - 40, $cy],
        [$cx - 20, $cy],
        [$cx - 10, $cy - 18],
        [$cx, $cy + 18],
        [$cx + 10, $cy - 10],
        [$cx + 20, $cy],
        [$cx + 40, $cy],
    ];
    for ($i = 0; $i < count($points) - 1; $i++) {
        imageline($img, $points[$i][0], $points[$i][1], $points[$i + 1][0], $points[$i + 1][1], $white);
    }
    // Circle around
    imagearc($img, $cx, $cy, $size * 0.75, $size * 0.75, 0, 360, $white);
    imagesetthickness($img, 1);
}

$iconDrawers = [
    'respiratory'   => 'drawRespiratoryIcon',
    'mobility'      => 'drawMobilityIcon',
    'beds_clinical' => 'drawBedsClinicalIcon',
    'diagnostic'    => 'drawDiagnosticIcon',
];

// Category Arabic labels for subtitle
$categoryArabic = [
    'respiratory'   => 'أجهزة تنفسية',
    'mobility'      => 'أجهزة حركة',
    'beds_clinical' => 'مستلزمات سريرية',
    'diagnostic'    => 'أجهزة فحص',
];

// ── 4. Generate device images ────────────────────────────────────────
$width  = 800;
$height = 600;
$generated = 0;

foreach ($devices as $id => [$name, $category]) {
    $rgb = $categoryColors[$category];

    // --- Main image ---
    $img = imagecreatetruecolor($width, $height);
    imagesavealpha($img, true);

    // Background gradient (top-to-bottom darkening)
    $topColor    = imagecolorallocate($img, $rgb[0], $rgb[1], $rgb[2]);
    $bottomColor = imagecolorallocate($img,
        (int)($rgb[0] * 0.6),
        (int)($rgb[1] * 0.6),
        (int)($rgb[2] * 0.6)
    );
    for ($y = 0; $y < $height; $y++) {
        $ratio = $y / $height;
        $r = (int)($rgb[0] * (1 - $ratio * 0.4));
        $g = (int)($rgb[1] * (1 - $ratio * 0.4));
        $b = (int)($rgb[2] * (1 - $ratio * 0.4));
        $lineColor = imagecolorallocate($img, $r, $g, $b);
        imageline($img, 0, $y, $width, $y, $lineColor);
    }

    // Subtle grid pattern
    $gridColor = imagecolorallocatealpha($img, 255, 255, 255, 90);
    for ($gx = 0; $gx < $width; $gx += 40) {
        imageline($img, $gx, 0, $gx, $height, $gridColor);
    }
    for ($gy = 0; $gy < $height; $gy += 40) {
        imageline($img, 0, $gy, $width, $gy, $gridColor);
    }

    // Draw category icon
    $iconY = 200;
    $iconSize = 140;
    $white = imagecolorallocate($img, 255, 255, 255);
    call_user_func($iconDrawers[$category], $img, $width / 2, $iconY, $iconSize);

    // Draw device name (Arabic)
    if ($fontPath) {
        // Large device name
        $fontSize = 28;
        $textColor = imagecolorallocate($img, 255, 255, 255);

        // Get bounding box for centering
        $bbox = imagettfbbox($fontSize, 0, $fontPath, $name);
        $textWidth  = abs($bbox[4] - $bbox[0]);
        $textX = (int)(($width - $textWidth) / 2);
        $textY = 340;

        // Shadow
        $shadowColor = imagecolorallocatealpha($img, 0, 0, 0, 60);
        imagettftext($img, $fontSize, 0, $textX + 2, $textY + 2, $shadowColor, $fontPath, $name);
        // Main text
        imagettftext($img, $fontSize, 0, $textX, $textY, $textColor, $fontPath, $name);

        // Category subtitle
        $subSize = 18;
        $subName = $categoryArabic[$category];
        $subBbox = imagettfbbox($subSize, 0, $fontPath, $subName);
        $subWidth = abs($subBbox[4] - $subBbox[0]);
        $subX = (int)(($width - $subWidth) / 2);
        $subY = 385;
        $subColor = imagecolorallocatealpha($img, 255, 255, 255, 60);
        imagettftext($img, $subSize, 0, $subX, $subY, $subColor, $fontPath, $subName);

        // Device ID badge
        $idText = "سند #$id";
        $idBbox = imagettfbbox(14, 0, $fontPath, $idText);
        $idWidth = abs($idBbox[4] - $idBbox[0]);
        $idX = (int)(($width - $idWidth) / 2);
        $idColor = imagecolorallocatealpha($img, 255, 255, 255, 80);
        imagettftext($img, 14, 0, $idX, 540, $idColor, $fontPath, $idText);
    }

    // Sanad watermark
    if ($fontPath) {
        $wmColor = imagecolorallocatealpha($img, 255, 255, 255, 85);
        imagettftext($img, 12, 0, 20, 580, $wmColor, $fontPath, 'سَنَد — Sanad Platform');
    }

    $mainFile = $basePath . "/devices/device_{$id}_main.jpg";
    imagejpeg($img, $mainFile, 90);
    imagedestroy($img);
    echo "[+] Created: $mainFile\n";
    $generated++;

    // --- Extra images for specific devices ---
    $extras = [];
    if ($id == 1)  $extras = ['side', 'charge'];
    if ($id == 4)  $extras = ['detail', 'remote'];
    if ($id == 7)  $extras = ['side'];
    if ($id == 8)  $extras = ['detail'];

    foreach ($extras as $suffix) {
        $img2 = imagecreatetruecolor($width, $height);

        // Slightly different hue shift
        $shift = ($suffix === 'side' || $suffix === 'detail') ? 20 : -20;
        $r2 = max(0, min(255, $rgb[0] + $shift));
        $g2 = max(0, min(255, $rgb[1] + $shift));
        $b2 = max(0, min(255, $rgb[2] + $shift));

        for ($y = 0; $y < $height; $y++) {
            $ratio = $y / $height;
            $r = (int)($r2 * (1 - $ratio * 0.4));
            $g = (int)($g2 * (1 - $ratio * 0.4));
            $b = (int)($b2 * (1 - $ratio * 0.4));
            $lineColor = imagecolorallocate($img2, $r, $g, $b);
            imageline($img2, 0, $y, $width, $y, $lineColor);
        }

        // Grid
        $gridColor2 = imagecolorallocatealpha($img2, 255, 255, 255, 90);
        for ($gx = 0; $gx < $width; $gx += 40) {
            imageline($img2, $gx, 0, $gx, $height, $gridColor2);
        }
        for ($gy = 0; $gy < $height; $gy += 40) {
            imageline($img2, 0, $gy, $width, $gy, $gridColor2);
        }

        // Icon
        call_user_func($iconDrawers[$category], $img2, $width / 2, 180, 120);

        // Label
        if ($fontPath) {
            $labelMap = [
                'side'   => 'عرض جانبي',
                'detail' => 'تفاصيل',
                'charge' => 'شاحن الجهاز',
                'remote' => 'الجهاز عن بُعد',
            ];
            $label = $labelMap[$suffix] ?? $suffix;
            $white2 = imagecolorallocate($img2, 255, 255, 255);
            $labelBbox = imagettfbbox(22, 0, $fontPath, $label);
            $labelWidth = abs($labelBbox[4] - $labelBbox[0]);
            $labelX = (int)(($width - $labelWidth) / 2);
            imagettftext($img2, 22, 0, $labelX, 350, $white2, $fontPath, $label);

            $subLabel = "$name — $label";
            $subBbox2 = imagettfbbox(16, 0, $fontPath, $subLabel);
            $subW2 = abs($subBbox2[4] - $subBbox2[0]);
            $subX2 = (int)(($width - $subW2) / 2);
            $subC2 = imagecolorallocatealpha($img2, 255, 255, 255, 60);
            imagettftext($img2, 16, 0, $subX2, 390, $subC2, $fontPath, $subLabel);
        }

        $extraFile = $basePath . "/devices/device_{$id}_{$suffix}.jpg";
        imagejpeg($img2, $extraFile, 90);
        imagedestroy($img2);
        echo "[+] Created: $extraFile\n";
        $generated++;
    }
}

// ── 5. Generate placeholder medical report PDFs (requests 1–10) ──────
// Minimal valid PDF with Arabic-compatible content (UTF-8 text).
for ($r = 1; $r <= 10; $r++) {
    $pdfFile = $basePath . "/medical-reports/report_{$r}.pdf";

    // Minimal valid PDF structure
    $pdf = "%PDF-1.4\n";
    $offsets = [];
    $objNum = 1;

    // Object 1: Catalog
    $offsets[] = strlen($pdf);
    $pdf .= "{$objNum} 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
    $objNum++;

    // Object 2: Pages
    $offsets[] = strlen($pdf);
    $pdf .= "{$objNum} 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
    $objNum++;

    // Object 3: Page
    $offsets[] = strlen($pdf);
    $pdf .= "{$objNum} 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>\nendobj\n";
    $objNum++;

    // Object 4: Content stream
    $content = "BT\n/F1 14 Tf\n72 720 Td\n(Sanad - Medical Report #$r) Tj\n0 -25 Td\n/F1 11 Tf\n(Temporary placeholder document) Tj\n0 -20 Td\n(This is a demo placeholder for seed data.) Tj\n0 -20 Td\n(Patient: Demo Patient #$r) Tj\n0 -20 Td\n(Date: 2025-01-01) Tj\n0 -20 Td\n(Category: General Medical) Tj\n0 -20 Td\n(Status: Pending Review) Tj\nET\n";
    $streamLen = strlen($content);
    $offsets[] = strlen($pdf);
    $pdf .= "{$objNum} 0 obj\n<< /Length {$streamLen} >>\nstream\n{$content}endstream\nendobj\n";
    $objNum++;

    // Object 5: Font
    $offsets[] = strlen($pdf);
    $pdf .= "{$objNum} 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
    $objNum++;

    // Cross-reference table
    $xrefOffset = strlen($pdf);
    $pdf .= "xref\n0 " . count($offsets) . "\n";
    $pdf .= "0000000000 65535 f \n";
    foreach ($offsets as $off) {
        $pdf .= sprintf("%010d 00000 n \n", $off);
    }

    // Trailer
    $pdf .= "trailer\n<< /Size " . count($offsets) . " /Root 1 0 R >>\n";
    $pdf .= "startxref\n{$xrefOffset}\n%%EOF\n";

    file_put_contents($pdfFile, $pdf);
    echo "[+] Created: $pdfFile\n";
    $generated++;
}

echo "\n========================================\n";
echo "  Generated {$generated} placeholder files.\n";
echo "  Devices: 15 main images + extras\n";
echo "  Reports: 10 placeholder PDFs\n";
echo "========================================\n";
