<?php
ob_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../inc/autoload.inc.php';

use App\Support\ImageCreator;

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (empty($_FILES['image'])) {
        throw new Exception('No file uploaded');
    }

    if (!isset($_POST['user_id'])) {
        throw new Exception('User ID missing');
    }

    $user_id = (int) $_POST['user_id'];
    $image = $_FILES['image'];

    $creator = new ImageCreator();
    $result = $creator->viewImage($image, $user_id);

    if ($result === false) {
        throw new Exception('Image processing failed');
    }

    $uploadDir = realpath(__DIR__ . "/../../") . '/content/post/random/';

    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        throw new Exception('Failed to create upload directory' . $uploadDir);
    }

    $filename = $result['filename'];
    $savePath = $uploadDir . $filename;

    if (!imagejpeg($result['new_image'], $savePath, 75)) {
        throw new Exception('Failed to save image');
    }

    imagedestroy($result['new_image']);
    imagedestroy($result['old_image']);

    $image_location = include __DIR__ . '/../Variables/image_location.php';

    echo json_encode([
        'success' => true,
        'image_url' => $image_location . 'content/post/random/' . $filename
    ]);
} 
catch (Exception $e) {
    $unexpectedOutput = ob_get_clean();
    error_log('ImageAPI Error: ' . $e->getMessage() . "\nOutput: " . $unexpectedOutput);

    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => trim($unexpectedOutput)
    ]);
    exit;
}
ob_end_flush();