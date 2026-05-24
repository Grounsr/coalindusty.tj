<?php
/**
 * Common CRUD helpers for the admin panel.
 */

/**
 * Save uploaded file to a category folder, returns relative web path or null.
 */
function admin_upload(string $field, string $category, array $allowed = ['image/jpeg','image/png','image/webp','image/svg+xml','application/pdf','video/mp4']): ?string
{
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) return null;
    $file = $_FILES[$field];
    $mime = mime_content_type($file['tmp_name']);
    if (!in_array($mime, $allowed, true)) return null;

    $extMap = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/svg+xml'=>'svg','application/pdf'=>'pdf','video/mp4'=>'mp4'];
    $ext = $extMap[$mime] ?? pathinfo($file['name'], PATHINFO_EXTENSION);

    $dir = UPLOAD_DIR . '/' . preg_replace('/[^a-z0-9_\-]/i', '', $category);
    @mkdir($dir, 0755, true);
    $fname = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $fname)) return null;

    DB::insert('media_library', [
        'file_path'     => '/uploads/' . $category . '/' . $fname,
        'original_name' => mb_substr($file['name'], 0, 250),
        'mime_type'     => $mime,
        'file_size'     => (int)$file['size'],
        'category'      => $category,
    ]);

    return '/uploads/' . $category . '/' . $fname;
}

function delete_file_safe(?string $relativePath): void
{
    if (!$relativePath) return;
    $p = ROOT_DIR . $relativePath;
    if (is_file($p) && strpos(realpath($p), realpath(UPLOAD_DIR)) === 0) {
        @unlink($p);
    }
}
