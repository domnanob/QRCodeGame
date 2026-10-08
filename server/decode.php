<?php
/**
 * decode.php
 * Receives an uploaded photo (qrImage), fixes rotation, resizes it,
 * decodes any QR code found, and returns JSON.
 */

header('Content-Type: application/json');

require __DIR__ . '/vendor/autoload.php';

use Zxing\QrReader;

// ---------- Config ----------
const MAX_DIMENSION   = 1000;   // resize longest side to this many px
const JPEG_QUALITY    = 90;
const MAX_UPLOAD_SIZE = 15 * 1024 * 1024; // 15 MB safety cap

// ---------- Helpers ----------

/**
 * Fix rotation for photos that store orientation in EXIF
 * instead of rotating the actual pixels (very common on phones).
 * Only works for JPEGs (EXIF doesn't apply to PNG).
 */
function fixOrientation(string $path, string $mime): void
{
    if ($mime !== 'image/jpeg' || !function_exists('exif_read_data')) {
        return;
    }

    $exif = @exif_read_data($path);
    if (!$exif || !isset($exif['Orientation'])) {
        return;
    }

    $image = @imagecreatefromjpeg($path);
    if (!$image) {
        return;
    }

    switch ($exif['Orientation']) {
        case 3:
            $image = imagerotate($image, 180, 0);
            break;
        case 6:
            $image = imagerotate($image, -90, 0);
            break;
        case 8:
            $image = imagerotate($image, 90, 0);
            break;
        default:
            imagedestroy($image);
            return; // no rotation needed
    }

    imagejpeg($image, $path, 95);
    imagedestroy($image);
}

/**
 * Resize the image down if it's larger than MAX_DIMENSION.
 * Returns the path to the (possibly new) file to decode.
 */
function resizeImage(string $srcPath, string $mime): string
{
    switch ($mime) {
        case 'image/jpeg':
            $src = @imagecreatefromjpeg($srcPath);
            break;
        case 'image/png':
            $src = @imagecreatefrompng($srcPath);
            break;
        case 'image/webp':
            $src = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($srcPath) : false;
            break;
        default:
            return $srcPath; // unsupported type for GD, let QrReader try raw
    }

    if (!$src) {
        return $srcPath; // couldn't load with GD, fall back to original
    }

    $width  = imagesx($src);
    $height = imagesy($src);

    if ($width <= MAX_DIMENSION && $height <= MAX_DIMENSION) {
        imagedestroy($src);
        return $srcPath; // already small enough
    }

    $ratio     = min(MAX_DIMENSION / $width, MAX_DIMENSION / $height);
    $newWidth  = max(1, (int) round($width * $ratio));
    $newHeight = max(1, (int) round($height * $ratio));

    $resized = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($resized, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    $tmpFile = tempnam(sys_get_temp_dir(), 'qr_') . '.jpg';
    imagejpeg($resized, $tmpFile, JPEG_QUALITY);

    imagedestroy($src);
    imagedestroy($resized);

    return $tmpFile;
}

function respond(bool $success, array $extra = []): void
{
    echo json_encode(array_merge(['success' => $success], $extra));
    exit;
}

// ---------- Main ----------

// Basic upload checks
if (!isset($_FILES['qrImage'])) {
    respond(false, ['error' => 'No file was uploaded.']);
}

$file = $_FILES['qrImage'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    $phpErrors = [
        UPLOAD_ERR_INI_SIZE   => 'File exceeds server upload limit.',
        UPLOAD_ERR_FORM_SIZE  => 'File exceeds form upload limit.',
        UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
        UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Server is missing a temp folder.',
        UPLOAD_ERR_CANT_WRITE => 'Server failed to write file to disk.',
        UPLOAD_ERR_EXTENSION  => 'Upload blocked by a server extension.',
    ];
    respond(false, ['error' => $phpErrors[$file['error']] ?? 'Unknown upload error.']);
}

if ($file['size'] > MAX_UPLOAD_SIZE) {
    respond(false, ['error' => 'File is too large.']);
}

$tmpPath = $file['tmp_name'];

// Verify it's actually an image and get its real mime type
$imageInfo = @getimagesize($tmpPath);
if ($imageInfo === false) {
    respond(false, ['error' => 'Uploaded file is not a valid image.']);
}
$mime = $imageInfo['mime'];

$allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
if (!in_array($mime, $allowedMimes, true)) {
    respond(false, ['error' => 'Unsupported image type: ' . $mime]);
}

// Work on a copy so we never touch PHP's original tmp file directly
$uploadTempDir = dirname($tmpPath);
$workPath = @tempnam($uploadTempDir, 'qrsrc_');
if ($workPath === false || $workPath === '') {
    respond(false, ['error' => 'Nem sikerült ideiglenes fájlt létrehozni a kép feldolgozásához.']);
}

if (!@copy($tmpPath, $workPath)) {
    @unlink($workPath);
    respond(false, ['error' => 'Nem sikerült előkészíteni a feltöltött képet feldolgozásra.']);
}

// Step 1: fix EXIF rotation (JPEG only)
fixOrientation($workPath, $mime);

// Step 2: resize to a decoder-friendly size
$decodePath = resizeImage($workPath, $mime);

// Step 3: attempt decode, with one retry using the original (unresized)
// image in case resizing hurt a very small/clean QR code.
$text = null;

try {
    $qrcode = new QrReader($decodePath);
    $result = $qrcode->text();
    if ($result !== false && $result !== '') {
        $text = $result;
    }
} catch (\Throwable $e) {
    // fall through to retry
}

if ($text === null && $decodePath !== $workPath) {
    try {
        $qrcode = new QrReader($workPath);
        $result = $qrcode->text();
        if ($result !== false && $result !== '') {
            $text = $result;
        }
    } catch (\Throwable $e) {
        // ignore, handled below
    }
}

// Cleanup temp files
@unlink($workPath);
if ($decodePath !== $workPath) {
    @unlink($decodePath);
}

if ($text === null) {
    respond(false, ['error' => 'Nem találtam QR kódot, tarts egyenesen az eszközt vagy vidd egy picit távolabb!']);
}

respond(true, ['text' => $text]);
