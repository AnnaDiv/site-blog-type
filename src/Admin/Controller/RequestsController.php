<?php

namespace App\Admin\Controller;
use App\Repository\RequestsRepository;

class RequestsController extends ManagerController {

    public function __construct(protected RequestsRepository $requestsRepository){}

    public function passwordChange(string $requestID){

        $errors = [];
        $requestID = (string) ($_GET['requestID'] ?? '');

        if (!empty($_POST)){

            $user_email = $this->requestsRepository->UserForPassChange($requestID);
            //echo 'user_email' . $user_email . ' <- <br>';
            $pass_one = test_input($_POST['pass_one'] ?? '');
            ///echo 'pass_one' . $pass_one; 
            if (!empty($pass_one)){

                $password = password_hash($pass_one, PASSWORD_DEFAULT);

                $updatePasswordOk = $this->requestsRepository->updatePass($user_email, $password);
                if ($updatePasswordOk === true){
                    
                    $this->requestsRepository->changeStatusPassRequest($requestID);
                    header("Location: index.php?" . http_build_query(['route' => 'admin' , 'pages' => 'login']));
                    exit;
                }
                $errors [] = 'something went wrong';
            }
            $errors [] = 'your pass is empty';
        }

        $this->render('password_reset', [
            'errors' => $errors,
            'requestID' => $requestID
        ]);
    }

    public function activateAccount(){

        $errors = [];
        $requestID = (string) ($_GET['requestID'] ?? '');

        $user_email = $this->requestsRepository->UserForAccActivation($requestID);

        if (!empty($user_email)){
            $this->requestsRepository->changeStatusAccount($user_email);
            $this->requestsRepository->deleteEntryAccVal($requestID);

            header("Location: index.php?" . http_build_query(['route' => 'admin' , 'pages' => 'login']));
            exit;
        }

        
    }
}