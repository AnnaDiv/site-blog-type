<?php
require_once __DIR__ . '../../../../inc/autoload.inc.php';
require_once __DIR__ . '/../../inc/db-connect.inc.php';

use App\Admin\Support\AuthService;
use App\Support\TokenService;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$email = $input['email'] ?? '';
$password = $input['password'] ?? '';

$auth = new AuthService($pdo);
$tokenService = new TokenService();

if ($auth->handleLogin($email, $password)) {
    $user_id = $_SESSION['usersID'];
    $nickname = $_SESSION['nickname'];

    $token = $tokenService->generate($user_id, $nickname);

    session_destroy(); // remove session-based tracking

    echo json_encode([
        'success' => true,
        'token' => $token,
        'user' => ['id' => $user_id, 'nickname' => $nickname]
    ]);
} 
else {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
}