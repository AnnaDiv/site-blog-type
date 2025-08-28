<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../../inc/db-connect.inc.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? '';

function getComments($pdo, $posts_id) {
    $posts_id = (int) $posts_id;
    $stmt = $pdo->prepare('SELECT `comments_id`, `users_nickname`, `contentComment`, DATE_FORMAT(`time`, "%Y-%m-%dT%H:%i:%s") AS `time` 
                            FROM comments 
                            WHERE `posts_id` = :posts_id 
                            ORDER BY `comments_id` DESC');
    $stmt->bindValue(':posts_id', $posts_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addComment($pdo, $contentComment, $posts_id, $users_nickname, $post_owner) {
    $stmt = $pdo->prepare('INSERT INTO `comments` (`posts_id`, `users_nickname`, `contentComment`) 
                           VALUES (:posts_id, :users_nickname, :contentComment)');
    $stmt->bindValue(':posts_id', (int) $posts_id, PDO::PARAM_INT);
    $stmt->bindValue(':users_nickname', $users_nickname);
    $stmt->bindValue(':contentComment', $contentComment);
    $stmt->execute();

    $comments_id = (int) $pdo->lastInsertId();

    $stmt = $pdo->prepare('UPDATE `posts` SET `comments` = `comments` + 1 WHERE `posts_id` = :posts_id');
    $stmt->bindValue(':posts_id', (int) $posts_id, PDO::PARAM_INT);
    $stmt->execute();

    if (addNotification($pdo, $post_owner, $users_nickname, 'comment'. $posts_id . '/' .$comments_id, $contentComment) !== true){
        return ['success' => false];
    }

    return ['success' => true];
}

function addNotification($pdo, string $post_owner, string $senders_nickname, string $place, string $contentComment): bool {
    $message = "{$senders_nickname} commented on your post: '{$contentComment}'";
    if (preg_match('/comment(\d+)\/(\d+)/', $place, $matches)) {
        $posts_id = $matches[1];
        $comments_id = $matches[2]; 
    }
    $link = "index.php?route=client&pages=post&post_id={$posts_id}#comment{$comments_id}";

    $stmt = $pdo->prepare('INSERT INTO `notification_actions` (`place`, `content`) 
                           VALUES (:place, :content)');
    $stmt->bindValue(':place', $place);
    $stmt->bindValue(':content', $message);
    $stmt->execute();

    $actions_id = (int) $pdo->lastInsertId();

    // Main notification row
    $stmt = $pdo->prepare('INSERT INTO `notifications` (`users_nickname`, `senders_nickname`, `actions_id`, `link`) 
                           VALUES (:users_nickname, :senders_nickname, :actions_id, :link)');
    $stmt->bindValue(':users_nickname', $post_owner);
    $stmt->bindValue(':senders_nickname', $senders_nickname);
    $stmt->bindValue(':link', $link);
    $stmt->bindValue(':actions_id', $actions_id, PDO::PARAM_INT);
    $stmt->execute();

    return true;
}

function removeComment($pdo, $posts_id, $comments_id) {
    $stmt = $pdo->prepare('DELETE FROM `comments` WHERE `posts_id`= :posts_id AND `comments_id`= :comments_id');
    $stmt->bindValue(':posts_id', (int) $posts_id, PDO::PARAM_INT);
    $stmt->bindValue(':comments_id', (int) $comments_id, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $pdo->prepare('UPDATE `posts` SET `comments` = `comments` - 1 WHERE `posts_id` = :posts_id');
    $stmt->bindValue(':posts_id', (int) $posts_id, PDO::PARAM_INT);
    $stmt->execute();

    return ['success' => true];
}

if ($method === 'GET' && $action === 'fetch') {
    $posts_id = $_GET['post_id'] ?? 0;
    //file_put_contents('debug.log', "Fetching comments for post_id: $posts_id\n", FILE_APPEND);
    echo json_encode(getComments($pdo, $posts_id));
    exit;
}

if ($method === 'POST' && $action === 'add') {
    $text = trim($_POST['comment'] ?? '');
    $posts_id = (int) ($_POST['post_id'] ?? 0);
    $users_nickname = (string) ($_SESSION['nickname'] ?? '');
    $post_owner = $_POST['post_owner'] ?? '';

    if ($text !== '' && $users_nickname !== '' && $posts_id !== '') {
        echo json_encode(addComment($pdo, $text, $posts_id, $users_nickname, $post_owner));
    } else {
        echo json_encode(['error' => 'Missing data']);
    }
    exit;
}

if ($method === 'POST' && $action === 'remove') {
    $posts_id = (int) ($_POST['post_id'] ?? 0);
    $comments_id = (int)($_POST['comments_id']);

    if ($comments_id !=='' && $posts_id !== '') {
        echo json_encode(removeComment($pdo, $posts_id, $comments_id));
    } else {
        echo json_encode(['error' => 'Missing data']);
    }
    exit;
}

echo json_encode(['error' => 'Invalid request']);