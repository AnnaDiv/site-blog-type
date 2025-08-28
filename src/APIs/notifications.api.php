<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../Repository/InboxRepository.php';
require_once __DIR__ . '/../../inc/db-connect.inc.php';

use App\Repository\InboxRepository;
$inbox = new InboxRepository($pdo);

if ($_GET['action'] === 'list') {
    try {
        $notifications = $inbox->notificationsByNickname($_SESSION['nickname']);
        echo json_encode($notifications);
    } 
    catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'mark_read') {

    $notification_id = (int)($_POST['notification_id'] ?? 0);

    if ($notification_id) {
        $inbox->changeNotificationStatus($notification_id);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Missing notification ID']);
    }
    exit;
}