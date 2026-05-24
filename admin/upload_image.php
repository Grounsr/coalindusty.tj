<?php
/**
 * TinyMCE image upload endpoint.
 */
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_crud.php';

header('Content-Type: application/json');

if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => ['message' => 'Upload failed']]);
    exit;
}

// TinyMCE sends as 'file' not arbitrary name — remap
$_FILES['upload'] = $_FILES['file'];
$path = admin_upload('upload', 'editor', ['image/jpeg','image/png','image/webp','image/svg+xml']);

if (!$path) {
    http_response_code(400);
    echo json_encode(['error' => ['message' => 'Invalid file type']]);
    exit;
}

echo json_encode(['location' => $path]);
