<?php
header('Content-Type: application/json');

require_once __DIR__ . '../../../inc/autoload.inc.php';
require_once __DIR__ . '/../Support/TokenService.php';
require_once __DIR__ . '../../../inc/db-connect.inc.php';

use App\Repository\EntriesRepository;
use App\Repository\CategoriesRepository;

// Token validation
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(['error' => 'Authorization token not provided']);
    exit;
}

$token = $matches[1];
$tokenService = new TokenService();
$decoded = $tokenService->validate($token);

if (!$decoded) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid or expired token']);
    exit;
}

$user_id = $decoded['sub'];
$nickname = $decoded['nickname'];

$entriesRepository = new EntriesRepository($pdo);
$categoriesRepository = new CategoriesRepository($pdo);

$allcategories = $categoriesRepository->allCategories();
$data = $_POST;
$image = $_FILES['image'] ?? null;

$title = @(string) test_input(($data['title'] ?? ''));
$description = @(string) test_input(($data['description'] ?? $data['content'] ?? ''));
$categories = json_decode($data['categories'] ?? '[]', true);
$post_status = @(string) test_input(($data['post_status'] ?? $data['status'] ?? 'private'));

$all_cats = array_map(fn($cat) => $cat['title'], $allcategories);
$final_categories = [];

foreach ($categories as $category) {
    $category = ucfirst(strtolower($category));
    if (in_array($category, $all_cats)) {
        $final_categories[] = $category;
    } 
    else {
        $stmt = $pdo->prepare('INSERT INTO `categories` (`title`, `description`) VALUES (:title, :description)');
        $stmt->bindValue(':title', $category);
        $stmt->bindValue(':description', ' ');
        try {
            $stmt->execute();
        } 
        catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'Duplicate')) {
                // already exists
            } 
            else {
                throw $e;
            }
        }
        $final_categories[] = $category;
    }
}

if (empty($final_categories)) $final_categories[] = 'none';

if (empty($image) || $image['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'Image upload failed or missing']);
    exit;
}

if (!empty($title) && !empty($description)) {
    $imageSubmit = $entriesRepository->imageProcessing($image, $user_id);

    if ($imageSubmit) {
        $createdOk = $entriesRepository->finalizing_posting(
            $imageSubmit, $user_id, $nickname, $title, $description, $final_categories, $post_status
        );
        if ($createdOk !== false) {
            echo json_encode(['success' => true, 'message' => 'Post created']);
            exit;
        }
    }
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create post']);
} 
else {
    http_response_code(400);
    echo json_encode(['error' => 'Title or description missing']);
}
