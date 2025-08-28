<?php

namespace App\Repository;

use PDO;
use DateTime;
use DateTimeZone;

class InboxRepository {

    public function __construct(private PDO $pdo){}

    public function notificationsByNickname(string $users_nickname){

        $stmt = $this->pdo->prepare('SELECT notifications.*, notification_actions.*, DATE_FORMAT(notifications.`time`, "%Y-%m-%dT%H:%i:%s") AS `formatted_time`
            FROM notifications
            LEFT JOIN notification_actions ON notification_actions.actions_id = notifications.actions_id
            WHERE notifications.users_nickname = :users_nickname
            GROUP BY notifications.actions_id
            ORDER BY notifications.`time` DESC');
        $stmt->bindValue(':users_nickname', $users_nickname);
        $stmt->execute();
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $notifications;
    }

    public function changeNotificationStatus(string $notification_id) {
        
        $dt = new DateTime('now', new DateTimeZone('Europe/Athens')); // or your preferred timezone
        $dt->modify('+1 day');
        $expires = $dt->format('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare('UPDATE `notifications` 
                                SET `used`=:used, `expires_at`= :expires
                                WHERE `notification_id`=:notification_id');
        $stmt->bindValue(':used', true, PDO::PARAM_BOOL);
        $stmt->bindValue(':expires', $expires);
        $stmt->bindValue(':notification_id', $notification_id, PDO::PARAM_INT);
        $stmt->execute();

        return true;
    }
}