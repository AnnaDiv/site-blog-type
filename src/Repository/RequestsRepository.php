<?php

namespace App\Repository;

use PDO;

class RequestsRepository {

    public function __construct(private PDO $pdo){}

    public function UserForPassChange(string $token){
        $used = 0;

        $stmt = $this->pdo->prepare('SELECT `email` FROM `password_resets` WHERE `token`=:token AND `used`=:used');
        $stmt->bindValue(':token', $token);
        $stmt->bindValue(':used', $used, PDO::PARAM_BOOL);
        $stmt->execute();

        $user_email = $stmt->fetchColumn();
        return $user_email;
    }

    public function updatePass(string $email, string $password){

        $stmt = $this->pdo->prepare('UPDATE `users` 
                                SET `password`=:password
                                WHERE `email`=:email');
        $stmt->bindValue(':password', $password);
        $stmt->bindValue(':email', $email);
        return $stmt->execute();
    }

    public function changeStatusPassRequest(string $token){
        $used = 1;

        $stmt = $this->pdo->prepare('UPDATE `password_resets` 
                                SET `used`=:used
                                WHERE `token`=:token');
        $stmt->bindValue(':used', $used, PDO::PARAM_BOOL);
        $stmt->bindValue(':token', $token);
        return $stmt->execute();
    }

    public function UserForAccActivation(string $token){

        $stmt = $this->pdo->prepare('SELECT `email` FROM `account_validation` WHERE `token`=:token');
        $stmt->bindValue(':token', $token);
        $stmt->execute();
        
        $user_email = $stmt->fetchColumn();
        return $user_email;
    }

    public function changeStatusAccount(string $email){

        $stmt = $this->pdo->prepare('UPDATE `users` 
                                SET `status`=:status
                                WHERE `email`=:email');
        $stmt->bindValue(':status', 'active');
        $stmt->bindValue(':email', $email);
        return $stmt->execute();
    }

    public function deleteEntryAccVal(string $token){

        $stmt = $this->pdo->prepare('DELETE FROM `account_validation` WHERE `token`=:token');
        $stmt->bindValue(':token', $token);
        return $stmt->execute();
    }
}