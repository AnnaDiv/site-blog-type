<?php

namespace App\Admin\Controller;
use App\Repository\UsersRepository;
use App\Admin\Support\PHPMailerService;

class UsersManagerController extends ManagerController {

    public function __construct(protected UsersRepository $usersRepository, protected PHPMailerService $phpMailerService){}

    public function userList(){
        $users = $this->usersRepository->users();

        $this->render_admin('users', [
            'users' => $users
        ]);
    }

    public function createUser(bool $status, bool $isadmin){
        $errors = [];

        if(!empty($_POST)){
            $nickname = @(string) test_input(($_POST['nickname'] ?? ''));
            $email = @(string) test_input(($_POST['email'] ?? ''));
            $pass_one = @(string) test_input(($_POST['pass_one'] ?? ''));
            $pass_two = @(string) test_input(($_POST['pass_two'] ?? ''));
            $motto = @(string) test_input(($_POST['motto'] ?? ''));

            if(!empty($nickname) && !empty($email) && !empty($pass_one) && !empty($pass_two)){
                $user_email_exists = '';
                $user_email_exists = $this->usersRepository->userByEmail($email);
                var_dump($user_email_exists);
                if (!empty($user_email_exists)){
                    $errors [] = "email already exists";
                }
                $user_nickname_exists = '';
                $user_nickname_exists = $this->usersRepository->user($nickname);
                if (!empty($user_nickname_exists)){
                    $errors [] = "nickname already exists";
                }
                if ($pass_one !== $pass_two){
                    $errors [] = "pass not the same";
                }
                if (empty($errors)){
                    $password = password_hash($pass_one, PASSWORD_DEFAULT);
                    $createdOk = $this->usersRepository->createUser($nickname, $email, $password, $motto);

                    if ($createdOk === true){
                        // Send activation email
                        $requestID = $this->phpMailerService->IDMakerAccVal($email);
                        $subject = 'Activate your account';
                        $link = "https://www.your_folder.gr/index.php?route=admin&pages=request_acc&requestID={$requestID}";

                        $body = "<p>Hello, </p>
                                <p>Click the link below to activate your account:</p>
                                <p><a href='{$link}'>Activate Account</a></p>
                                <p>If you didn't request this, please ignore this email.</p>";

                        $this->phpMailerService->sendMail($email, $subject, $body);

                        // Then redirect
                        header("Location: index.php?" . http_build_query(['route' => 'admin' , 'pages' => 'email/sent/acc']));
                        exit;
                    }
                }
            }
            else {
                $errors [] = "you are missing something";
            }
        }

        $this->render('create.login', [
            'errors' => $errors,
            'status' => $status,
            'isadmin' => $isadmin
        ]);
    }

    public function deleteUser(string $nickname, bool $status, bool $isadmin){
        $errors = [];

        $user = $this->usersRepository->user($nickname);
        
        if (!empty($_POST)){
            $deletedOk = $this->usersRepository->deleteUser($nickname);
            if ($deletedOk === true){

                $subject = 'your account has been terminated';
                $body = "<p>Hello, </p>
                        <p>Your account was violating site policy and has been terminated</p>
                        <p>This decision is irrevocable, your data has been expunged from our platform.</p>";

                $this->phpMailerService->sendMail($user['email'], $subject, $body);
                header("Location: index.php?" . http_build_query(['route' => 'admin' , 'pages' => 'edit/users']));
                exit;
            }
            else {
                $errors [] = "couldnt delete user";
            }
        }

        $this->render_admin('delete.user', [
            'user' => $user,
            'errors' => $errors,
            'status' => $status,
            'isadmin' => $isadmin
        ]);

    }

    public function userByNicknameAPI(string $nickname){

        $user = $this->usersRepository->userByNicknameAPI($nickname);

        if (!empty($user)) {
            return $this->json($user, 200);
        } 
        else {
            return $this->json(['error' => 'user not found'], 404);
        } 
    }

}