<?php
require_once __DIR__ . '/../../inc/db-connect.inc.php';

$stmt = $pdo->prepare("DELETE FROM notifications WHERE expires_at IS NOT NULL AND expires_at < NOW()");
$stmt->execute();

echo "Deleted expired notifications at " . date('Y-m-d H:i:s');