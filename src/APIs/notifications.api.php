<?php
/*error_reporting(E_ALL);
ini_set('display_errors', 1); // Show errors in browser
ini_set('log_errors', 1);     // Log errors to file
ini_set('error_log', __DIR__ . '/error.log'); // Write to a specific file */
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
        error_log('API Error: ' . $e->getMessage()); // Write to error log
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