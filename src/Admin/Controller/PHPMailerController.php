<?php

namespace App\Admin\Controller;

use App\Admin\Support\PHPMailerService;

class PHPMailerController extends ManagerController {

    public function __construct(protected PHPMailerService $phpMailerService){}

    public function resetPassword(bool $status, bool $isadmin){
        $errors = [];

        if(!empty($_POST)){
            $email = test_input($_POST['email'] ?? '');

            if ($this->phpMailerService->validEmail($email) !== false ){

                $requestID = $this->phpMailerService->IDMaker($email);
                $subject = 'Reset Your Password';

                $link = "https://www.your_folder.gr/index.php?route=admin&pages=request_pass&requestID={$requestID}";
                
                $body = "<p>Hello, </p>
                    <p>Click the link below to reset your password:</p>
                    <p><a href='{$link}'>Reset Your Password</a></p>
                    <p>If you didn't request this, please ignore this email.</p>";
                    
                $this->phpMailerService->sendMail($email, $subject, $body);
                header("Location: index.php?" . http_build_query(['route' => 'admin' , 'pages' => 'email/sent']));
                exit;
            }
            $errors [] = 'There is no account with this email';
        }

        $this->render('help.login', [
            'errors' => $errors,
            'status' => $status,
            'isadmin' => $isadmin
        ]);
    }

}