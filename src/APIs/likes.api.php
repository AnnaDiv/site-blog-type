<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../inc/db-connect.inc.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? '';
$users_id = (int) ($_SESSION['usersID'] ?? 0);

function getLike($pdo, $posts_id, $users_id) {
    $stmt = $pdo->prepare('SELECT `like` FROM likes WHERE `posts_id` = :posts_id AND `users_id` = :users_id');
    $stmt->bindValue(':posts_id', $posts_id, PDO::PARAM_INT);
    $stmt->bindValue(':users_id', $users_id, PDO::PARAM_INT);
    $stmt->execute();
    return (int) $stmt->fetchColumn();  // 1 if liked, 0 if no like
}

function getTotalLikes($pdo, $posts_id) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM likes WHERE `posts_id` = :posts_id AND `like` = 1');
    $stmt->bindValue(':posts_id', $posts_id, PDO::PARAM_INT);
    $stmt->execute();
    $likes = $stmt->fetchColumn();

    $stmt = $pdo->prepare('UPDATE posts SET `likes` = :likes WHERE `posts_id` = :posts_id');
    $stmt->bindValue(':likes', $likes);
    $stmt->bindValue(':posts_id', $posts_id, PDO::PARAM_INT);
    $stmt->execute();
    return $likes;
}

function toggleLike($pdo, $posts_id, $users_id, $post_owner, $users_nickname) {
    $stmt = $pdo->prepare('SELECT `like` FROM likes WHERE `posts_id` = :posts_id AND `users_id` = :users_id');
    $stmt->bindValue(':posts_id', $posts_id, PDO::PARAM_INT);
    $stmt->bindValue(':users_id', $users_id, PDO::PARAM_INT);
    $stmt->execute();
    $existingLike = $stmt->fetchColumn();

    $newLike = ($existingLike == 1) ? 0 : 1;

    if ($existingLike !== false) {
        $stmt = $pdo->prepare('UPDATE likes SET `like` = :like WHERE `posts_id` = :posts_id AND `users_id` = :users_id');
    } else {
        $stmt = $pdo->prepare('INSERT INTO likes (`like`, `posts_id`, `users_id`) VALUES (:like, :posts_id, :users_id)');
    }

    $stmt->bindValue(':like', $newLike);
    $stmt->bindValue(':posts_id', $posts_id, PDO::PARAM_INT);
    $stmt->bindValue(':users_id', $users_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($newLike === 1 && $users_nickname !== $post_owner) {
        addNotification($pdo, $post_owner, $users_nickname, $posts_id);
    }

    return ['success' => true];
}

function addNotification($pdo, string $post_owner, string $senders_nickname, int $posts_id): bool {
    if ($post_owner === $senders_nickname) return true;

    $link = "index.php?route=client&pages=post&post_id={$posts_id}";
    $message = "{$senders_nickname} liked your post";

    $stmt = $pdo->prepare('INSERT INTO notification_actions (`place`, `content`) VALUES (:place, :content)');
    
    $stmt->bindValue(':place', "like{$posts_id}");
    $stmt->bindValue(':content', $message);
    $stmt->execute();

    $actions_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare('INSERT INTO notifications (`users_nickname`, `senders_nickname`, `actions_id`, `link`) 
                           VALUES (:users_nickname, :senders_nickname, :actions_id, :link)');
    
    $stmt->bindValue(':users_nickname', $post_owner);
    $stmt->bindValue(':senders_nickname', $senders_nickname);
    $stmt->bindValue(':actions_id', $actions_id);
    $stmt->bindValue(':link', $link);
    $stmt->execute();

    return true;
}

if ($method === 'GET' && $action === 'getLikes') {
    $posts_id = (int) ($_GET['post_id'] ?? 0);

    if (!$posts_id) {
        echo json_encode(['error' => 'Missing post ID']);
        exit;
    }

    $like = getLike($pdo, (int) $posts_id, (int) $users_id);
    $totalLikes = getTotalLikes($pdo, (int) $posts_id);

    echo json_encode([$like, $totalLikes]);
    exit;
}

if ($method === 'POST' && $action === 'like') {
    $posts_id = (int) ($_POST['post_id'] ?? 0);
    $post_owner = $_POST['post_owner'] ?? '';
    $users_nickname = $_SESSION['nickname'] ?? '';

    if (!$posts_id || !$users_id) {
        echo json_encode(['success' => false, 'error' => 'You are not logged in.']);
        exit;
    }

    $result = toggleLike($pdo, (int) $posts_id, (int) $users_id, $post_owner, $users_nickname);
    echo json_encode($result);
    exit;
}

echo json_encode(['error' => 'Invalid request']);
exit;