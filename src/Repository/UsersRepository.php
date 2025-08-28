<?php

namespace App\Repository;

use PDO;
use App\Model\UserModel;
use App\Support\ImageCreator;

class UsersRepository {

    public function __construct(private PDO $pdo){}

    public function users(){

        $stmt = $this->pdo->prepare('SELECT `users_id`, `nickname` FROM `users`');
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $users;
    }

    public function user(string $nickname) : bool | UserModel {

        $stmt = $this->pdo->prepare('SELECT * FROM `users` WHERE `nickname`=:nickname');
        $stmt->bindValue(':nickname', $nickname);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, UserModel::class);
        $user = $stmt->fetch();
        return $user;
    }

    public function userByNicknameAPI(string $nickname){ //Used in API

        $stmt = $this->pdo->prepare('SELECT * FROM `users` WHERE `nickname`=:nickname');
        $stmt->bindValue(':nickname', $nickname);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, UserModel::class);
        $user = $stmt->fetch();

        $commentsStmt = $this->pdo->prepare('SELECT COUNT(*) as `count` FROM comments WHERE users_nickname = :users_nickname');
        $commentsStmt->bindValue(':users_nickname', $nickname);
        $commentsStmt->execute();
        $comments = $commentsStmt->fetch(PDO::FETCH_ASSOC);
        $commentCount = (int)$comments['count'];

        $likesStmt = $this->pdo->prepare('SELECT COUNT(*) as `count` FROM likes WHERE users_id = :users_id');
        $likesStmt->bindValue(':users_id', $user->users_id);
        $likesStmt->execute();
        $likes = $likesStmt->fetch(PDO::FETCH_ASSOC);
        $likesCount = (int)$likes['count'];

        $postsStmt = $this->pdo->prepare('SELECT COUNT(*) as `count` FROM posts WHERE user_nickname = :user_nickname');
        $postsStmt->bindValue(':user_nickname', $nickname);
        $postsStmt->execute();
        $postsCount = $postsStmt->fetch(PDO::FETCH_ASSOC);
        $postsCount = (int)$postsCount['count'];

        $image_location = include __DIR__ . '/../Variables/image_location.php';

        return ['Nickname' => $user->nickname, 'Motto' => $user->motto, 'Profile image' => $image_location . $user->image_folder, 'Comments' => $commentCount, 'Likes' => $likesCount, 'Number of posts' => $postsCount];
    }

    public function userByEmail(string $email) : bool | UserModel {

        $stmt = $this->pdo->prepare('SELECT * FROM `users` WHERE `email`=:email');
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, UserModel::class);
        $user = $stmt->fetch();
        return $user;
    }

    public function profileOwner(string $nickname){

        $user = $this->user($nickname);

        if ($user->nickname === $_SESSION['nickname']) {
            return true;
        }
        return false;
    }

    public function updateUser(array|bool $res, int $users_id, string $nickname, string $email, string $motto, string $password){

        if(is_array($res)){

            $image_uploaded = imagejpeg($res['new_image'], $res['destination']);

            imagedestroy($res['old_image']);
            imagedestroy($res['new_image']);

            if ($image_uploaded === true) {
                $this->updateUserFields($users_id, $nickname, $email, $motto, $password);
                $this->updateUserImage($res['image_folder'], $users_id);
                return true;
            }
            else {
                return false;
            }
        }
        else {
            
            $this->updateUserFields($users_id, $nickname, $email, $motto, $password);
            return true;
        }

    }

    public function updateUserImage(string $res, int $users_id){

        $image_folder = $res;
        $stmt = $this->pdo->prepare('UPDATE `users` SET `image_folder`= :image_folder WHERE `users_id`= :users_id');
        $stmt->bindValue(':image_folder', $image_folder);
        $stmt->bindValue(':users_id', $users_id, PDO::PARAM_INT);
        return $stmt->execute();

    }

    public function updateUserFields(int $users_id, string $nickname, string $email, string $motto, string $password){

        if (empty($password)){
            $stmt = $this->pdo->prepare('UPDATE `users` 
                                SET `nickname`=:nickname, `motto`=:motto, `email`=:email
                                WHERE `users_id`=:users_id');
        }
        else {
            $stmt = $this->pdo->prepare('UPDATE `users` 
                                SET `nickname`=:nickname, `motto`=:motto, `email`=:email, `password`= :password
                                WHERE `users_id`=:users_id');
            $stmt->bindValue(':password', $password);
        }
        $stmt->bindValue(':nickname', $nickname);
        $stmt->bindValue(':motto', $motto);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':users_id', $users_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function createUser(string $nickname, string $email, string $password, string $motto){

        if ($motto === ''){
            $motto = ' ';
        }
        $stmt = $this->pdo->prepare('INSERT INTO `users` (`nickname`, `motto`, `email`, `password`) 
                                    VALUES ( :nickname, :motto, :email, :password)');
        $stmt->bindValue(':nickname', $nickname);
        $stmt->bindValue(':motto', $motto);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $password);
        return $stmt->execute();
    }

    public function deleteUser(string $nickname) {

        $stmt = $this->pdo->prepare('DELETE FROM `users` WHERE `nickname`=:nickname');
        $stmt->bindValue(':nickname', $nickname);
        return $stmt->execute();
    }

    public function imageProcessingProf(array $image, int $users_id){

        $creator = new ImageCreator();
        $result = $creator->createImageProf($image, $users_id);
        return $result;
    }

    public function usersByQuote(int $perPage, string $quote){
        
        $page = test_input((int) ($_GET['page'] ?? 1));
        $offset = ($page-1)*$perPage;
        $like = '%' . $quote . '%';

        $stmt = $this->pdo->prepare('SELECT users.users_id, users.nickname, users.image_folder, users.motto
                    FROM users
                    WHERE users.status = :status
                        AND (users.nickname LIKE :quote 
                            OR users.motto LIKE :quote)
                    GROUP BY users.nickname
                    ORDER BY users.nickname DESC
                    LIMIT :perPage OFFSET :offset');

        $stmt->bindValue(':status', 'active');
        $stmt->bindValue(':quote', $like);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        $profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $results = []; 
        foreach ($profiles AS $profile){
            $commentsStmt = $this->pdo->prepare('SELECT COUNT(*) as `count` FROM comments WHERE users_nickname = :users_nickname');
            $commentsStmt->bindValue(':users_nickname', $profile['nickname']);
            $commentsStmt->execute();
            $comments = $commentsStmt->fetch(PDO::FETCH_ASSOC);
            $commentCount = (int)$comments['count'];

            $likesStmt = $this->pdo->prepare('SELECT COUNT(*) as `count` FROM likes WHERE users_id = :users_id');
            $likesStmt->bindValue(':users_id', $profile['users_id']);
            $likesStmt->execute();
            $likes = $likesStmt->fetch(PDO::FETCH_ASSOC);
            $likesCount = (int)$likes['count'];

            $postsStmt = $this->pdo->prepare('SELECT COUNT(*) as `count` FROM posts WHERE user_nickname = :user_nickname');
            $postsStmt->bindValue(':user_nickname', $profile['nickname']);
            $postsStmt->execute();
            $postsCount = $postsStmt->fetch(PDO::FETCH_ASSOC);
            $postsCount = (int)$postsCount['count'];

            $results[] = [
                'nickname' => $profile['nickname'],
                'image_folder' => $profile['image_folder'],
                'motto' => $profile['motto'],
                'comments_count' => (int) $comments['count'],
                'likes_count' => (int) $likesCount,
                'posts_count' => (int) $postsCount,
            ];
        }

        return $results;
    }

    public function num_users_quote(int $perPage, string $quote) {
        $like = '%' . $quote . '%';

        $stmt = $this->pdo->prepare('SELECT COUNT(DISTINCT `users_id`)
                    FROM users
                    WHERE users.status = :status
                        AND (users.nickname LIKE :quote 
                            OR users.motto LIKE :quote)
                    GROUP BY users.nickname');

        $stmt->bindValue(':status', 'active');
        $stmt->bindValue(':quote', $like);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $page_num = ceil($count/$perPage);  
    }

    public function isBlocked ($user_nickname, $blockingUser){

        $stmt = $this->pdo->prepare('SELECT `status` FROM `blocks` 
                                        WHERE `user_nickname`=:user_nickname 
                                            AND `blockingUser`= :blockingUser');
        $stmt->bindValue(':user_nickname', $user_nickname);
        $stmt->bindValue(':blockingUser', $blockingUser);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function followersByNickname(int $perPage, string $nickname){

        $stmt = $this->pdo->prepare('SELECT users.nickname, users.image_folder, users.motto
                             FROM users
                             INNER JOIN follows ON follows.follower = users.nickname
                             WHERE follows.user_nickname = :nickname AND follows.status = 1
                             ORDER BY follows.follower');

        $stmt->bindValue(':nickname', $nickname);
        $stmt->execute();

        $followers = $stmt->FetchAll(PDO::FETCH_ASSOC);
        return $followers;
    }

    public function followersByNickname_pages(int $perPage, string $nickname){

        $stmt = $this->pdo->prepare('SELECT COUNT(*) AS `count`
                             FROM users
                             INNER JOIN follows ON follows.follower = users.nickname
                             WHERE follows.user_nickname = :nickname AND follows.status = 1
                             ORDER BY follows.follower');
        $stmt->bindValue(':nickname', $nickname);
        $stmt->execute();

        $followers_num = $stmt->fetchColumn();
        return $page_num = ceil($followers_num/$perPage);
    }

    public function followingByNickname(int $perPage, string $nickname){

        $stmt = $this->pdo->prepare('SELECT users.nickname, users.image_folder, users.motto
                             FROM users
                             INNER JOIN follows ON follows.user_nickname = users.nickname
                             WHERE follows.follower = :nickname AND follows.status = 1
                             ORDER BY follows.user_nickname');
        $stmt->bindValue(':nickname', $nickname);
        $stmt->execute();

        $following = $stmt->FetchAll(PDO::FETCH_ASSOC);
        return $following;
    }

    public function followingByNickname_pages(int $perPage, string $nickname){

        $stmt = $this->pdo->prepare('SELECT COUNT(*) AS `count`
                             FROM users
                             INNER JOIN follows ON follows.user_nickname = users.nickname
                             WHERE follows.follower = :nickname AND follows.status = 1');
        $stmt->bindValue(':nickname', $nickname);
        $stmt->execute();
        
        $following_num = $stmt->fetchColumn();
        return $page_num = ceil($following_num/$perPage);
    }

}