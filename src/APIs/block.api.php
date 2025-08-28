<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../../inc/db-connect.inc.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? '';

function getBlock(PDO $pdo, string $profileUser, string $blockingUser) {
    $stmt = $pdo->prepare('SELECT status FROM blocks 
                            WHERE user_nickname = :user_nickname 
                              AND blockingUser = :blockingUser');
    $stmt->bindValue(':user_nickname', $profileUser, PDO::PARAM_STR);
    $stmt->bindValue(':blockingUser', $blockingUser, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchColumn();
    return $result;
}

function toggleBlock(PDO $pdo, string $profileUser, string $blockingUser) {
    $isBlocked = getBlock($pdo, $profileUser, $blockingUser);

    $newBlock = ($isBlocked == 1) ? 0 : 1;

    if ($isBlocked !== false) {
        $stmt = $pdo->prepare('UPDATE blocks 
                               SET status = :status 
                               WHERE user_nickname = :user_nickname 
                                 AND blockingUser = :blockingUser');
    }
    else {
        $stmt = $pdo->prepare('INSERT INTO blocks (user_nickname, blockingUser, status) 
                               VALUES (:user_nickname, :blockingUser, :status)');
    }
    $stmt->bindValue(':user_nickname', $profileUser, PDO::PARAM_STR);
    $stmt->bindValue(':blockingUser', $blockingUser, PDO::PARAM_STR);
    $stmt->bindValue(':status', $newBlock, PDO::PARAM_BOOL);
    $stmt->execute();

    if ($newBlock == 1) {
        $stmt = $pdo->prepare('UPDATE follows 
                               SET status = 0
                               WHERE user_nickname = :user_nickname 
                                 AND follower = :follower');
        $stmt->bindValue(':user_nickname', $profileUser, PDO::PARAM_STR);
        $stmt->bindValue(':follower', $blockingUser, PDO::PARAM_STR);
        $stmt->execute();
    }

    return ['success' => true];
}


if ($method === 'GET' && $action === 'getBlock') {
    $profileUser = $_GET['profileUser'] ?? '';
    $blockingUser = $_GET['blockingUser'] ?? '';

    if (!$profileUser || !$blockingUser) {
        echo json_encode(['error' => 'Missing profile user or blockingUser nickname']);
        exit;
    }

    $isBlocked = getBlock($pdo, $profileUser, $blockingUser);

    echo json_encode([$isBlocked]);
    exit;
}

if ($method === 'POST' && $action === 'block') {
    $profileUser = $_POST['profileUser'] ?? '';
    $blockingUser = $_POST['blockingUser'] ?? '';

    if (!$profileUser || !$blockingUser) {
        echo json_encode(['success' => false, 'error' => 'You are not logged in.']);
        exit;
    }

    echo json_encode(toggleBlock($pdo, $profileUser, $blockingUser));
    exit;
}

echo json_encode(['error' => 'Invalid request']);
exit;
