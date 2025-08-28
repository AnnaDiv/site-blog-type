<?php

namespace App\Admin\Support;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PDO;
use DateTime;
use DateTimeZone;

class PHPMailerService {

    public function __construct(private PHPMailer $phpMailer, private PDO $pdo) {}

    public function sendMail(string $receiver, string $subject, string $body){

        $mail = $this->phpMailer;

        try {

            $mail->isSMTP();
            $mail->Host       = 'mail.your_server.gr'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'email@server.gr'; 
            $mail->Password   = 'password';
            $mail->SMTPSecure = 'tls'; // or 'ssl'
            $mail->Port       = 587; // 465 for SSL, 587 for TLS

            $mail->setFrom('email@server.gr', 'username');
            $mail->addAddress($receiver);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send(); 

        } 
        catch (Exception $e) {
            echo "Message could not be sent. Error: {$mail->ErrorInfo}";
        }
    }

    public function validEmail(string $email){
        
        $stmt = $this->pdo->prepare('SELECT `email` FROM `users` WHERE `email`=:email'); 
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $res = $stmt->fetchColumn();
        return $res;
    }

    public function IDMaker(string $email){

        $token = $this->requestID();
        $dt = new DateTime('now', new DateTimeZone('Europe/Athens'));
        $dt->modify('+1 hour');
        $expires = $dt->format('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare("INSERT INTO password_resets ( `email`, `token`, `expires_at`) 
                                VALUES (:email, :token, :expires_at)");
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':token', $token);
        $stmt->bindValue(':expires_at', $expires);
        $stmt->execute();

        return $token;
    }

    public function IDMakerAccVal(string $email){

        $token = $this->requestID();

        $stmt = $this->pdo->prepare("INSERT INTO account_validation ( `email`, `token`) 
                                VALUES (:email, :token )");
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':token', $token);
        $stmt->execute();

        return $token;
    }

    private function requestID(){
        return bin2hex(random_bytes(40));
    }
    
}
