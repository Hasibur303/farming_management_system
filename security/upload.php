<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function clamav_scan(string $path): array
{
    $configured = trim((string) env('CLAMAV_PATH', ''));
    $candidates = array_filter([
        $configured,
        'C:\\Program Files\\ClamAV\\clamscan.exe',
        'C:\\ClamAV\\clamscan.exe',
    ]);

    $scanner = null;
    foreach ($candidates as $candidate) {
        if (is_file($candidate)) {
            $scanner = $candidate;
            break;
        }
    }

    if ($scanner === null) {
        $pathMatches = [];
        $lookupExitCode = 1;
        $lookupCommand = PHP_OS_FAMILY === 'Windows' ? 'where.exe clamscan.exe 2>NUL' : 'command -v clamscan 2>/dev/null';
        exec($lookupCommand, $pathMatches, $lookupExitCode);
        $pathCandidate = trim((string) ($pathMatches[0] ?? ''));
        if ($lookupExitCode === 0 && $pathCandidate !== '' && is_file($pathCandidate)) {
            $scanner = $pathCandidate;
        }
    }

    if ($scanner === null) {
        security_log('upload.malware_scanner_unavailable', [], 'notice');
        $required = filter_var(env('CLAMAV_REQUIRED', 'false'), FILTER_VALIDATE_BOOL);
        return ['available' => false, 'clean' => !$required, 'output' => 'ClamAV is unavailable'];
    }

    $command = escapeshellarg($scanner) . ' --no-summary --infected ' . escapeshellarg($path) . ' 2>&1';
    $output = [];
    $exitCode = 2;
    exec($command, $output, $exitCode);

    return [
        'available' => true,
        'clean' => $exitCode === 0,
        'infected' => $exitCode === 1,
        'output' => substr(implode("\n", $output), 0, 2000),
    ];
}

function secure_image_upload(array $file, string $destinationDirectory, int $maxBytes = 5_242_880): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('The file upload did not complete successfully.');
    }
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        security_log('upload.suspicious_source');
        throw new RuntimeException('Invalid uploaded file.');
    }
    if (($file['size'] ?? 0) < 1 || (int) $file['size'] > $maxBytes) {
        security_log('upload.size_rejected', ['size' => (int) ($file['size'] ?? 0)]);
        throw new RuntimeException('The image must be smaller than 5 MB.');
    }

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = (string) $finfo->file($file['tmp_name']);
    $originalExtension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
    if (!isset($allowed[$mime]) || !in_array($originalExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
        security_log('upload.type_rejected', ['mime' => $mime, 'extension' => $originalExtension]);
        throw new RuntimeException('Only genuine JPG, PNG, GIF, or WebP images are allowed.');
    }
    if (@getimagesize($file['tmp_name']) === false) {
        security_log('upload.invalid_image', ['mime' => $mime]);
        throw new RuntimeException('The uploaded file is not a valid image.');
    }

    $quarantine = dirname(__DIR__) . '/storage/quarantine';
    if (!is_dir($quarantine) && !mkdir($quarantine, 0750, true) && !is_dir($quarantine)) {
        throw new RuntimeException('Unable to prepare upload quarantine.');
    }
    $quarantinePath = $quarantine . '/' . bin2hex(random_bytes(20)) . '.upload';
    if (!move_uploaded_file($file['tmp_name'], $quarantinePath)) {
        throw new RuntimeException('Unable to quarantine uploaded file.');
    }

    $scan = clamav_scan($quarantinePath);
    if (!$scan['clean']) {
        @unlink($quarantinePath);
        security_log(!empty($scan['infected']) ? 'upload.malware_detected' : 'upload.scan_failed', ['scanner_output' => $scan['output']]);
        throw new RuntimeException(!empty($scan['infected']) ? 'The uploaded file contains malware.' : 'The uploaded file could not be scanned safely.');
    }

    if (!is_dir($destinationDirectory) && !mkdir($destinationDirectory, 0755, true) && !is_dir($destinationDirectory)) {
        @unlink($quarantinePath);
        throw new RuntimeException('Unable to prepare upload storage.');
    }
    $filename = bin2hex(random_bytes(20)) . '.' . $allowed[$mime];
    $destination = rtrim($destinationDirectory, '/\\') . DIRECTORY_SEPARATOR . $filename;
    if (!rename($quarantinePath, $destination)) {
        @unlink($quarantinePath);
        throw new RuntimeException('Unable to store the uploaded file.');
    }
    @chmod($destination, 0640);
    security_log('upload.accepted', ['filename' => $filename, 'mime' => $mime, 'scanner_available' => $scan['available']], 'info');

    return ['filename' => $filename, 'path' => $destination, 'mime' => $mime];
}

