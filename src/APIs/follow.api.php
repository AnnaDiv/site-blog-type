<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../../inc/db-connect.inc.php'; // adjust if needed

$method = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? '';

function getTotalFollowers(PDO $pdo, string $profileUser) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM follows 
                            WHERE user_nickname = :user_nickname 
                                AND status = 1');
    $stmt->bindValue(':user_nickname', $profileUser, PDO::PARAM_STR);
    $stmt->execute();
    return (int)$stmt->fetchColumn();
}

function getFollow(PDO $pdo, string $profileUser, string $follower) {
    $stmt = $pdo->prepare('SELECT status FROM follows 
                            WHERE user_nickname = :user_nickname 
                              AND follower = :follower');
    $stmt->bindValue(':user_nickname', $profileUser, PDO::PARAM_STR);
    $stmt->bindValue(':follower', $follower, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchColumn();
    return $result;
}

function toggleFollow(PDO $pdo, string $profileUser, string $follower) {
    $isFollowing = getFollow($pdo, $profileUser, $follower);

    $newFollow = ($isFollowing == 1) ? 0 : 1;

    if ($isFollowing !== false) {
        $stmt = $pdo->prepare('UPDATE follows 
                               SET status = :status 
                               WHERE user_nickname = :user_nickname 
                                 AND follower = :follower');
    }
    else {
        $stmt = $pdo->prepare('INSERT INTO follows (user_nickname, follower, status) 
                               VALUES (:user_nickname, :follower, :status)');
    }
    $stmt->bindValue(':user_nickname', $profileUser, PDO::PARAM_STR);
    $stmt->bindValue(':follower', $follower, PDO::PARAM_STR);
    $stmt->bindValue(':status', $newFollow, PDO::PARAM_BOOL);
    $stmt->execute();

    if ($isFollowing !== true){
        addNotification($pdo, $profileUser, $follower);
    }

    return ['success' => true];
}

function addNotification($pdo, string $profileUser, string $follower): bool {

    $link = "index.php?route=client&pages=profile&nickname={$follower}&page=1";
    $message = "{$follower} followed you";

    $stmt = $pdo->prepare('INSERT INTO notification_actions (`place`, `content`) VALUES (:place, :content)');
    $stmt->execute([
        ':place' => "follow($profileUser}",
        ':content' => $message
    ]);
    $actions_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare('INSERT INTO notifications (`users_nickname`, `senders_nickname`, `actions_id`, `link`) 
                           VALUES (:users_nickname, :senders_nickname, :actions_id, :link)');
    $stmt->execute([
        ':users_nickname' => $profileUser,
        ':senders_nickname' => $follower,
        ':actions_id' => $actions_id,
        ':link' => $link
    ]);

    return true;
}


if ($method === 'GET' && $action === 'getFollow') {
    $profileUser = $_GET['profileUser'] ?? '';
    $follower = $_GET['follower'] ?? '';

    if (!$profileUser || !$follower) {
        echo json_encode(['error' => 'Missing profile user or follower nickname']);
        exit;
    }

    $followers = getTotalFollowers($pdo, $profileUser);
    $isFollowing = getFollow($pdo, $profileUser, $follower);

    echo json_encode([$isFollowing, $followers]);
    exit;
}

if ($method === 'POST' && $action === 'follow') {
    $profileUser = $_POST['profileUser'] ?? '';
    $follower = $_POST['follower'] ?? '';

    if (!$profileUser || !$follower) {
        echo json_encode(['success' => false, 'error' => 'You are not logged in.']);
        exit;
    }

    echo json_encode(toggleFollow($pdo, $profileUser, $follower));
    exit;
}

echo json_encode(['error' => 'Invalid request']);
exit;
