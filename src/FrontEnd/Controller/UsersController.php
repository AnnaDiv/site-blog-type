<?php

namespace App\FrontEnd\Controller;

use App\Repository\UsersRepository;

class UsersController extends ClientController {

    public function __construct(protected UsersRepository $usersRepository){}

    public function editUser(string $nickname, bool $isadmin, bool $status){
        
        $errors = [];
        $user = $this->usersRepository->user($nickname);

        if (!empty($_POST)){
            $new_nickname = test_input((string) ($_POST['nickname'] ?? ''));
            $motto = test_input((string) ($_POST['motto'] ?? ''));
            $new_email = test_input((string) ($_POST['email'] ?? ''));
            $old_pass = test_input((string) ($_POST['old_pass'] ?? ''));
            $new_pass = test_input((string) ($_POST['new_pass'] ?? ''));
            $current_pass = $user->password;

            $new_image = [];

            if (empty($new_nickname) || empty($new_email)){
                $errors [] = "nickname, email or both are empty";
            }
            if (empty($old_pass) && empty($new_pass)){
                $new_password = '';
                // we arent changing pass
            }
            elseif (empty($old_pass) && !empty($new_pass)) {
                $errors [] = "please give your old password";
            }
            elseif (!empty($old_pass) && empty($new_pass)) {
                $new_password = '';
                //ignore since browsers tend to autofill if its is stored
            }
            else {
                if (password_verify($old_pass, $current_pass) === true ){
                    $new_password = password_hash($new_pass, PASSWORD_DEFAULT);
                }
                else {
                    $errors []= "password mismatch";
                }
            }       
            if ($new_nickname !== $nickname){
                $user_exists = '';
                $user_exists = $this->usersRepository->user($new_nickname);
                if (!empty($user_exists)){
                    $errors []= "nickname taken";
                }
            }
            if (empty($motto)){
                $motto = ' ';
            }
            //if there is an image
            if ( !empty($_FILES) && !empty($_FILES['image']) ){
                $image = $_FILES['image'];
            }

            if (!empty($image['name'])){
                $imageSubmit = $this->usersRepository->imageProcessingProf($image, $user->users_id);
                if (!empty($imageSubmit)){
                    //$updatedOk = $this->entriesRepository->update_posting($imageSubmit, $post_id, $title, $description, $final_categories, $post_status);
                }
                else {
                    $errors []= "something is wrong with your image, submit a different one";
                }
            }

            if (empty($errors)){

                if (!empty($imageSubmit)){
                    $updateOk = $this->usersRepository->updateUser($imageSubmit, $user->users_id, $new_nickname, $new_email, $motto, $new_password);
                }
                else {
                    $updateOk = $this->usersRepository->updateUser(false, $user->users_id, $new_nickname, $new_email, $motto, $new_password);
                }
                if ($updateOk === true){
                    if ($isadmin === true){
                        if ($_SESSION['usersID'] == $user->users_id){
                            $_SESSION['nickname'] = $new_nickname;
                        }
                    }
                    else {
                        $_SESSION['nickname'] = $new_nickname;
                    }
                    header("Location: index.php?" . http_build_query(['route' => 'client' , 'pages' => 'profile', 'nickname' => $new_nickname]));
                    exit;
                }   
                else {
                    $errors [] = 'something went wrong in the update';
                }
            }
        }

        $this->render('edit.profile', [
            'user' => $user,
            'errors' => $errors,
            'isadmin' => $isadmin,
            'status'=> $status,
            'nickname' => $nickname
        ]);
    }

    public function search_user(int $perPage, bool $status, bool $isadmin, string $quote) {
        
        $errors = [];
        $page = test_input((int)($_GET['page'] ?? 1));
        $quote = test_input(($_GET['search_q'] ?? ''));

        $users = $this->usersRepository->usersByQuote($perPage, $quote);
        $num_pages = $this->usersRepository->num_users_quote($perPage, $quote);

        $this->render('search_user', [
            'users' => $users,
            'errors' => $errors,
            'status' => $status,
            'isadmin' => $isadmin,
            'page' => $page,
            'num_pages' => $num_pages
        ]);
    }

    public function ShowFollowers(int $perPage, bool $status, bool $isadmin, string $nickname){

        $errors = [];
        $page = test_input((int)($_GET['page'] ?? 1));

        $users = $this->usersRepository->followersByNickname($perPage, $nickname);
        $num_pages = $this->usersRepository->followersByNickname_pages($perPage, $nickname);

        $this->render('followers', [
            'users' => $users,
            'errors' => $errors,
            'status' => $status,
            'isadmin' => $isadmin,
            'page' => $page,
            'num_pages' => $num_pages
        ]);
    }

    public function ShowFollowing(int $perPage, bool $status, bool $isadmin, string $nickname){

        $errors = [];
        $page = test_input((int)($_GET['page'] ?? 1));

        $users = $this->usersRepository->followingByNickname($perPage, $nickname);
        $num_pages = $this->usersRepository->followingByNickname_pages($perPage, $nickname);

        $this->render('following', [
            'users' => $users,
            'errors' => $errors,
            'status' => $status,
            'isadmin' => $isadmin,
            'page' => $page,
            'num_pages' => $num_pages
        ]);
    }

}