<?php

namespace App\Repository;

use PDO;
use App\Model\UserModel;
use App\Model\CommentModel;
use App\Support\ImageCreator;

class EntriesRepository {

    public function __construct(private PDO $pdo){}


    public function browse(int $perPage, array $excludedUsers = []) : array {

        $page = test_input((int) ($_GET['page'] ?? 1));
        $offset = ($page - 1) * $perPage;

        $notInClause = $this->notInClause($excludedUsers);

        $sql = 'SELECT posts.posts_id, posts.user_nickname, posts.title, posts.content, posts.image_folder, 
                    posts.likes, posts.comments, DATE_FORMAT(posts.`time`, "%Y-%m-%dT%H:%i:%s") AS `time`,
                    GROUP_CONCAT(percategory.category_title ORDER BY percategory.category_title SEPARATOR ", ") AS categories
                FROM posts
                LEFT JOIN percategory ON percategory.post_id = posts.posts_id
                WHERE deleted = :deleted AND status = :status AND `type`= :type ' . $notInClause . '
                GROUP BY posts.posts_id
                ORDER BY posts.posts_id DESC 
                LIMIT :perPage OFFSET :offset';

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':deleted', false, PDO::PARAM_BOOL);
        $stmt->bindValue(':status', 'public');
        $stmt->bindValue(':type', 'post');

        foreach ($excludedUsers as $index => $username) {
            $stmt->bindValue(":excluded{$index}", $username, PDO::PARAM_STR);
        }

        $stmt->execute();
        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $entries = $this->Post_Morph($entries);
        return $entries;
    }

    public function Post_Morph(array $entries) : array {
        $res_entries = [];
        foreach ($entries AS $entry) {
            $cat_names = explode(', ', $entry['categories']);
            $entry['categories'] = $cat_names;
            $res_entries [] = $entry;
        }
        return $res_entries;
    }

    public function num_Pages_browse(int $perPage, array $excludedUsers = []) : int {
        $notInClause = $this->notInClause($excludedUsers);

        $sql = 'SELECT COUNT(*) AS `count` 
                FROM posts
                WHERE deleted = :deleted 
                AND status = :status 
                AND `type` = :type ' . $notInClause;

        $stmtCount = $this->pdo->prepare($sql);

        $stmtCount->bindValue(':deleted', false, PDO::PARAM_BOOL);
        $stmtCount->bindValue(':status', 'public');
        $stmtCount->bindValue(':type', 'post');

        foreach ($excludedUsers as $index => $username) {
            $stmtCount->bindValue(":excluded{$index}", $username, PDO::PARAM_STR);
        }

        $stmtCount->execute();
        $count = (int) $stmtCount->fetchColumn();
        $num_pages = ceil($count / $perPage);

        return $num_pages;
    }

    public function notInClause(array $excludedUsers = []){

        $placeholders = [];
        foreach ($excludedUsers as $index => $username) {
            $placeholders[] = ":excluded{$index}";
        }

        $notInClause = '';
        if (!empty($placeholders)) {
            $notInClause = ' AND posts.user_nickname NOT IN (' . implode(', ', $placeholders) . ')';
        }
        return $notInClause;
    }

    public function excludedUsers(string|bool $nickname){
        if ($nickname === false){
            return [];
        }
        $stmt = $this->pdo->prepare('SELECT * FROM `blocks`
                WHERE (`user_nickname` = :user_nickname OR `blockingUser` = :blockingUser) AND `status`= 1');
        $stmt->bindValue(':user_nickname', $nickname);
        $stmt->bindValue(':blockingUser', $nickname);     
        $stmt->execute();
        $names = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $excludedUsers = [];
        if ($names !== false){
            foreach($names AS $name){
                if ($name['user_nickname'] === $nickname){
                    $excludedUsers[] = $name['blockingUser'];
                }
                elseif ($name['blockingUser'] === $nickname){
                    $excludedUsers[] = $name['user_nickname'];
                }
            }
        }
        return $excludedUsers;
    }

    public function browsePerCategory(int $perPage, array $excludedUsers = []) : ?array {

        $notInClause = $this->notInClause($excludedUsers);

        $category_name = (string) test_input(($_GET['category'] ?? ''));
        $page =  test_input((int) ($_GET['page'] ?? 1));
        $offset = ($page-1)*$perPage;
        $status = 'public';
        //echo $category_name;
        $stmt = $this->pdo->prepare('SELECT posts.posts_id, posts.user_nickname, posts.title, posts.content, posts.image_folder, posts.likes, posts.comments, posts.time, DATE_FORMAT(posts.`time`, "%Y-%m-%dT%H:%i:%s") AS `time`,
                                GROUP_CONCAT(DISTINCT pc2.category_title ORDER BY pc2.category_title SEPARATOR ", ") AS categories
                                FROM posts
                                INNER JOIN percategory pc1 ON posts.posts_id = pc1.post_id
                                INNER JOIN percategory pc2 ON posts.posts_id = pc2.post_id
                                WHERE pc1.category_title = :title
                                AND posts.deleted = :deleted
                                AND posts.status = :status
                                AND `type`= :type ' . $notInClause . '
                                GROUP BY posts.posts_id
                                ORDER BY posts.posts_id DESC
                                LIMIT :perPage OFFSET :offset');
        $stmt->bindValue(':title', $category_name);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':deleted', false , PDO::PARAM_BOOL);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':type', 'post');

        foreach ($excludedUsers as $index => $username) {
            $stmt->bindValue(":excluded{$index}", $username, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function num_pages_per_cat(int $perPage, array $excludedUsers = []) : ?int {

        $notInClause = $this->notInClause($excludedUsers);

        $category_name = (string) test_input(($_GET['category'] ?? ''));
        $status = 'public';
        $stmt = $this->pdo->prepare('SELECT COUNT(DISTINCT posts.posts_id) AS total
                FROM posts
                INNER JOIN percategory ON posts.posts_id = percategory.post_id
                WHERE percategory.category_title = :title 
                    AND deleted= :deleted 
                    AND status= :status 
                    AND `type`= :type' . $notInClause);
        $stmt->bindValue(':title', $category_name);
        $stmt->bindValue(':deleted', false , PDO::PARAM_BOOL);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':type', 'post');
        
        foreach ($excludedUsers as $index => $username) {
            $stmt->bindValue(":excluded{$index}", $username, PDO::PARAM_STR);
        }

        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $page_num = ceil($count/$perPage);
    }

    public function postByID(int $posts_id){
        $stmt = $this->pdo->prepare('SELECT posts.posts_id, posts.user_nickname, posts.title, posts.content, posts.image_folder, posts.status, posts.deleted,
                                GROUP_CONCAT(percategory.category_title ORDER BY percategory.category_title SEPARATOR ", ") AS categories
                                FROM posts
                                LEFT JOIN percategory ON percategory.post_id = posts.posts_id
                                WHERE posts_id= :posts_id
                                GROUP BY posts.posts_id');
        $stmt->bindValue(':posts_id', $posts_id , PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createPost(int $user_id, string $nickname, string $title, string $content, array $categories, string $image_folder, string $status, string $type){

        $res = [];
        $stmt = $this->pdo->prepare('INSERT INTO `posts` ( `user_nickname`, `title`, `content`, `image_folder`, `status`, `type`) 
                                    VALUES ( :user_nickname, :title, :content, :image_folder, :status, :type)');
        $stmt->bindValue(':user_nickname', $nickname);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':content', $content);
        $stmt->bindValue(':image_folder', $image_folder);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':type', $type);
        $res = $stmt->execute();
        $posts_id = (int) $this->pdo->lastInsertId();

        foreach ($categories AS $category){
            $stmt = $this->pdo->prepare('INSERT INTO `percategory` ( `category_title`, `post_id`) 
                                        VALUES ( :category, :post_id)');
            $stmt->bindValue(':category', $category);
            $stmt->bindValue(':post_id', $posts_id, PDO::PARAM_INT);
            $stmt->execute();
        }

        return $res;
    }

    public function updatePostFields (int $post_id, string $title, string $content, array $categories, string $status){

        $stmt = $this->pdo->prepare('UPDATE `posts` 
                                SET `title`=:title, `content`=:content, `status`=:status
                                WHERE `posts_id`=:post_id');
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':content', $content);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();

        $this->delete_percategory_ByPostId($post_id);

        foreach ($categories AS $category){
            $stmt = $this->pdo->prepare('INSERT INTO `percategory` ( `category_title`, `post_id`) 
                                        VALUES ( :category, :post_id)');
            $stmt->bindValue(':category', $category);
            $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->execute();
        
        }

    }

    public function delete_percategory_ByPostId(int $post_id){

        $stmt = $this->pdo->prepare('DELETE FROM percategory WHERE `post_id`=:post_id');
        $stmt->bindValue(':post_id', $post_id);
        $stmt->execute();

    }

    public function imageProcessing(array $image, string $user_id): array|false {

        $creator = new ImageCreator();
        $result = $creator->createImage($image, $user_id);
        return $result;

    }


    public function finalizing_posting(array $res, int $user_id, string $nickname, string $title, string $description, array $categories, string $post_status, string $type){

        $image_uploaded = imagejpeg($res['new_image'], $res['destination']);

        imagedestroy($res['old_image']);
        imagedestroy($res['new_image']);

        if ($image_uploaded === true) {
            $this->createPost($user_id, $nickname, $title, $description, $categories, $res['image_folder'], $post_status, $type);
        }
        else{
            return false;
        }
    }

    public function update_posting(array | bool $res, int $post_id, string $title, string $description, array $final_categories, string $post_status){

        if(is_array($res)){

            $image_uploaded = imagejpeg($res['new_image'], $res['destination']);

            imagedestroy($res['old_image']);
            imagedestroy($res['new_image']);

            if ($image_uploaded === true) {
                $this->updatePostFields($post_id, $title, $description, $final_categories, $post_status);
                $this->updatePostImage($res['image_folder'], $post_id);
            }
            else {
                return false;
            }
        }
        else {

            $this->updatePostFields($post_id, $title, $description, $final_categories, $post_status);
        }
    }

    public function updatePostImage(string $image_folder, int $posts_id){

        $stmt = $this->pdo->prepare('UPDATE `posts` SET `image_folder`= :image_folder WHERE `posts_id`= :posts_id');
        $stmt->bindValue(':image_folder', $image_folder);
        $stmt->bindValue(':posts_id', $posts_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function catByPostId(int $post_id){

        $stmt = $this->pdo->prepare('SELECT * FROM `percategory` WHERE `post_id` = :post_id');
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function clientDeletePost(int $posts_id){
        
        $stmt = $this->pdo->prepare('UPDATE `posts` SET `deleted`=:deleted  WHERE `posts_id`= :posts_id');
        $stmt->bindValue(':deleted', true, PDO::PARAM_BOOL);
        $stmt->bindValue(':posts_id', $posts_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleted_status(int $posts_id) : bool {

        $stmt = $this->pdo->prepare('SELECT `deleted` FROM `posts` WHERE `posts_id` = :posts_id');
        $stmt->bindValue(':posts_id', $posts_id, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetchColumn();
        return $res;
    }

    public function is_public(int $posts_id){

        $stmt = $this->pdo->prepare('SELECT `status` FROM `posts` WHERE `posts_id` = :posts_id');
        $stmt->bindValue(':posts_id', $posts_id, PDO::PARAM_INT);
        $stmt->execute();
        $status = $stmt->fetchColumn();
        if ($status === 'public'){
            return true;
        }
        else {
            return false;
        }
    }

    public function userByNickname(string $nickname){

        $stmt = $this->pdo->prepare('SELECT * FROM `users` WHERE `nickname`=:nickname');
        $stmt->bindValue(':nickname', $nickname);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, UserModel::class);
        $user = $stmt->fetch();
        return $user;
    }

    public function isowner($post_id) {

        $entry = $this->postByID((int)$post_id);
        
        if($entry['user_nickname'] === $_SESSION['nickname']){
            return true;
        }
        return false;
    }

    public function allEntriesByNickname(string $user_nickname, int $perPage){

        $page = test_input((int) ($_GET['page'] ?? 1));
        $offset = ($page-1)*$perPage;
        $stmt = $this->pdo->prepare('SELECT posts.posts_id, posts.user_nickname, posts.title, posts.content, posts.image_folder, posts.status, posts.deleted, 
                                GROUP_CONCAT(percategory.category_title ORDER BY percategory.category_title SEPARATOR ", ") AS categories
                                FROM posts
                                INNER JOIN percategory ON percategory.post_id = posts.posts_id
                                WHERE posts.user_nickname = :user_nickname
                                GROUP BY posts.posts_id
                                ORDER BY posts.posts_id DESC 
                                LIMIT :perPage OFFSET :offset');
        $stmt->bindValue(':user_nickname', $user_nickname);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allEntriesByNickname_num(string $user_nickname, int $perPage){

        $stmt = $this->pdo->prepare('SELECT COUNT(DISTINCT posts.posts_id) AS count
                            FROM posts 
                            WHERE posts.user_nickname = :user_nickname');
        $stmt->bindValue(':user_nickname', $user_nickname);
        $stmt->execute();

        $count = $stmt->fetchColumn();
        return $page_num = ceil($count/$perPage);
    }

    public function showEntriesByNickname_n_Deleted(string $user_nickname, bool $deleted, int $perPage){

        $page = test_input((int) ($_GET['page'] ?? 1));
        $offset = ($page-1)*$perPage;

        $stmt = $this->pdo->prepare('SELECT posts.posts_id, posts.user_nickname, posts.title, posts.content, posts.image_folder, posts.status 
                                GROUP_CONCAT(percategory.category_title ORDER BY percategory.category_title SEPARATOR ", ") AS categories
                                FROM posts
                                INNER JOIN percategory ON percategory.post_id = posts.posts_id
                                WHERE posts.user_nickname = :user_nickname AND `deleted`=:deleted
                                GROUP BY posts.posts_id
                                ORDER BY posts.posts_id DESC 
                                LIMIT :perPage OFFSET :offset');
        $stmt->bindValue(':deleted', $deleted, PDO::PARAM_BOOL);
        $stmt->bindValue(':user_nickname', $user_nickname);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function showEntriesByNickname_n_Deleted_num(string $user_nickname, bool $deleted, int $perPage){

        $stmt = $this->pdo->prepare('SELECT COUNT(DISTINCT `posts_id`) AS `count`
                                FROM posts
                                WHERE `user_nickname` = :user_nickname AND `deleted`=:deleted');
        $stmt->bindValue(':deleted', $deleted, PDO::PARAM_BOOL);
        $stmt->bindValue(':user_nickname', $user_nickname);
        $stmt->execute();

        $count = $stmt->fetchColumn();
        return $page_num = ceil($count/$perPage);
    }   

    public function showEntriesByNickname_n_Deleted_n_PublicStatus(string $user_nickname, bool $deleted, string $status, int $perPage){

        $page = test_input((int) ($_GET['page'] ?? 1));
        $offset = ($page-1)*$perPage;

        $stmt = $this->pdo->prepare('SELECT posts.posts_id, posts.user_nickname, posts.title, posts.content, posts.image_folder, posts.status, 
                                GROUP_CONCAT(percategory.category_title ORDER BY percategory.category_title SEPARATOR ", ") AS categories
                                FROM posts
                                INNER JOIN percategory ON percategory.post_id = posts.posts_id
                                WHERE posts.user_nickname = :user_nickname AND `deleted`=:deleted AND `status`=:status
                                GROUP BY posts.posts_id
                                ORDER BY posts.posts_id DESC 
                                LIMIT :perPage OFFSET :offset');

        $stmt->bindValue(':deleted', $deleted, PDO::PARAM_BOOL);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':user_nickname', $user_nickname);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function showEntriesByNickname_n_Deleted_n_PublicStatus_num(string $user_nickname, bool $deleted, string $status, int $perPage){

        $stmt = $this->pdo->prepare('SELECT COUNT(DISTINCT `posts_id`) AS `count`
                                FROM posts
                                WHERE `user_nickname` = :user_nickname AND `deleted`=:deleted AND `status`=:status');
        
        $stmt->bindValue(':deleted', $deleted, PDO::PARAM_BOOL);
        $stmt->bindValue(':user_nickname', $user_nickname);
        $stmt->bindValue(':status', $status);
        $stmt->execute();

        $count = $stmt->fetchColumn();
        return $page_num = ceil($count/$perPage);        
    }

    public function showEntriesByNickname_n_Deleted_n_PublicStatusAPI(string $user_nickname){

        $stmt = $this->pdo->prepare('SELECT posts.posts_id, posts.user_nickname, posts.title, posts.content, posts.image_folder, posts.status, 
                                GROUP_CONCAT(percategory.category_title ORDER BY percategory.category_title SEPARATOR ", ") AS categories
                                FROM posts
                                INNER JOIN percategory ON percategory.post_id = posts.posts_id
                                WHERE posts.user_nickname = :user_nickname AND `deleted`=:deleted AND `status`=:status
                                GROUP BY posts.posts_id
                                ORDER BY posts.posts_id DESC');

        $stmt->bindValue(':deleted', false, PDO::PARAM_BOOL);
        $stmt->bindValue(':status', 'public');
        $stmt->bindValue(':user_nickname', $user_nickname);
        $stmt->execute();

        return $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function reinstatePost(int $posts_id){

        $stmt = $this->pdo->prepare('UPDATE `posts` SET `deleted`=:deleted  WHERE `posts_id`= :posts_id');
        $stmt->bindValue(':deleted', false, PDO::PARAM_BOOL);
        $stmt->bindValue(':posts_id', $posts_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deletedPosts(int $perPage){
    
        $page = test_input((int) ($_GET['page'] ?? 1));
        $offset = ($page-1)*$perPage;

        $stmt = $this->pdo->prepare('SELECT * FROM `posts` 
                                WHERE `deleted`=:deleted
                                ORDER BY posts.posts_id DESC 
                                LIMIT :perPage OFFSET :offset');

        $stmt->bindValue(':deleted', true, PDO::PARAM_BOOL);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deletedPosts_num(int $perPage){

        $stmt = $this->pdo->prepare('SELECT COUNT(DISTINCT `posts_id`) AS `count`
                                FROM posts
                                WHERE `deleted`=:deleted');
        $stmt->bindValue(':deleted', true, PDO::PARAM_BOOL);
        $stmt->execute();

        $count = $stmt->fetchColumn();
        return $page_num = ceil($count/$perPage); 
    }

    public function permaDeletePost(int $posts_id){

        $stmt = $this->pdo->prepare('DELETE FROM `posts` WHERE `posts_id`=:posts_id');
        $stmt->bindValue(':posts_id', $posts_id);
        return $stmt->execute();
    }

    public function postsByQuote(int $perPage, string $quote, array $excludedUsers = []){

        $page = test_input((int) ($_GET['page'] ?? 1));
        $offset = ($page-1)*$perPage;

        $notInClause = $this->notInClause($excludedUsers);

        $like = '%' . $quote . '%';

        $stmt = $this->pdo->prepare('SELECT posts.posts_id, posts.user_nickname, posts.title, posts.content, 
                    posts.image_folder, posts.likes, posts.comments, DATE_FORMAT(posts.`time`, "%Y-%m-%d %H:%i:%s") AS `time`,
                    GROUP_CONCAT(percategory.category_title ORDER BY percategory.category_title SEPARATOR ", ") AS categories
                    FROM posts
                    LEFT JOIN percategory ON percategory.post_id = posts.posts_id
                    WHERE posts.deleted = :deleted AND posts.status = :status
                        AND (posts.user_nickname LIKE :quote 
                            OR posts.title LIKE :quote 
                            OR posts.content LIKE :quote)' . $notInClause . '
                    GROUP BY posts.posts_id
                    ORDER BY posts.posts_id DESC
                    LIMIT :perPage OFFSET :offset');

        $stmt->bindValue(':deleted', false, PDO::PARAM_BOOL);
        $stmt->bindValue(':status', 'public');
        $stmt->bindValue(':quote', $like);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        foreach ($excludedUsers as $index => $username) {
            $stmt->bindValue(":excluded{$index}", $username, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function num_Pages_quote(int $perPage, string $quote, array $excludedUsers = []){
        
        $page = test_input((int) ($_GET['page'] ?? 1));
        $offset = ($page-1)*$perPage;

        $notInClause = $this->notInClause($excludedUsers);

        $like = '%' . $quote . '%';
        
        $stmt = $this->pdo->prepare('SELECT COUNT(DISTINCT `posts_id`) AS `count`
                    FROM posts
                    WHERE `deleted` = :deleted AND `status` = :status
                        AND (`user_nickname` LIKE :quote 
                            OR `title` LIKE :quote 
                            OR `content` LIKE :quote)' . $notInClause );

        $stmt->bindValue(':deleted', false, PDO::PARAM_BOOL);
        $stmt->bindValue(':status', 'public');
        $stmt->bindValue(':quote', $like);

        foreach ($excludedUsers as $index => $username) {
            $stmt->bindValue(":excluded{$index}", $username, PDO::PARAM_STR);
        }

        $stmt->execute();
        $count = $stmt->fetchColumn();

        return $page_num = ceil($count/$perPage);  
    }

    public function art(int $perPage, string $artist){

        $page = test_input((int) ($_GET['page'] ?? 1));
        $offset = ($page-1)*$perPage;

        $stmt = $this->pdo->prepare('SELECT posts.posts_id, posts.user_nickname, posts.title, posts.content, posts.image_folder, 
                            posts.likes, posts.comments, DATE_FORMAT(posts.`time`, "%Y-%m-%dT%H:%i:%s") AS `time`,
                            GROUP_CONCAT(percategory.category_title ORDER BY percategory.category_title SEPARATOR ", ") AS categories
                            FROM posts
                            LEFT JOIN percategory ON percategory.post_id = posts.posts_id
                            WHERE deleted = :deleted AND status = :status AND `type`= :type AND posts.user_nickname= :artist
                            GROUP BY posts.posts_id
                            ORDER BY posts.posts_id DESC 
                            LIMIT :perPage OFFSET :offset');

        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':deleted', false , PDO::PARAM_BOOL);
        $stmt->bindValue(':status', 'public');
        $stmt->bindValue(':type', 'art');
        $stmt->bindValue(':artist', $artist);
        $stmt->execute();

        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $entries = $this->Post_Morph($entries);

        return $entries;
    }

    public function num_art(int $perPage, string $artist){

        $stmtCount = $this->pdo->prepare('SELECT COUNT(*) AS `count` FROM `posts` WHERE `deleted`= :deleted AND `status`= :status AND `type`= :type AND `user_nickname`=:artist');
        $stmtCount->bindValue(':deleted', false , PDO::PARAM_BOOL);
        $stmtCount->bindValue(':status', 'public');
        $stmtCount->bindValue(':type', 'art');
        $stmtCount->bindValue(':artist', $artist);
        $stmtCount->execute();

        $count = $stmtCount->fetchColumn();
        $num_pages = ceil($count / $perPage);

        return $num_pages;
    }

    public function artBanner(){

        $stmt = $this->pdo->prepare('SELECT `image_folder` FROM posts WHERE `type`=:type AND `user_nickname`= :artist');
        $stmt->bindValue(':type', 'art');
        $stmt->bindValue(':artist', 'Anna');
        $stmt->execute();

        $art_images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $art_images;
    }

    public function owner_by_postID(int $posts_id){

        $stmt = $this->pdo->prepare('SELECT `user_nickname` FROM posts WHERE `posts_id`=:posts_id');
        $stmt->bindValue(':posts_id', $posts_id);
        $stmt->execute();
        return $user_nickname = $stmt->fetchColumn();
    }

    public function LikedPosts(int $perPage, int $users_id){

        $page = test_input((int) ($_GET['page'] ?? 1));
        $offset = ($page-1)*$perPage;

        $stmt = $this->pdo->prepare('SELECT posts.posts_id, posts.user_nickname, posts.title, posts.content, posts.image_folder, 
                            posts.likes, posts.comments, DATE_FORMAT(posts.`time`, "%Y-%m-%dT%H:%i:%s") AS `time`,
                            GROUP_CONCAT(percategory.category_title ORDER BY percategory.category_title SEPARATOR ", ") AS categories
                            FROM posts
                            INNER JOIN likes ON likes.posts_id = posts.posts_id
                            LEFT JOIN percategory ON percategory.post_id = posts.posts_id
                            WHERE deleted = :deleted AND likes.users_id = :users_id AND likes.like = 1
                            GROUP BY posts.posts_id
                            ORDER BY posts.posts_id DESC 
                            LIMIT :perPage OFFSET :offset');

        $stmt->bindValue(':deleted', false , PDO::PARAM_BOOL);
        $stmt->bindValue(':users_id', $users_id);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $entries;
    }

    public function LikedPosts_pages(int $perPage, int $users_id) {

        $page = test_input((int) ($_GET['page'] ?? 1));
        $offset = ($page-1)*$perPage;

        $stmt = $this->pdo->prepare('SELECT COUNT(*) AS `count` FROM posts
                            INNER JOIN likes ON likes.posts_id = posts.posts_id
                            WHERE deleted = :deleted AND likes.users_id = :users_id AND likes.like = 1');
        $stmt->bindValue(':deleted', false , PDO::PARAM_BOOL);
        $stmt->bindValue(':users_id', $users_id);
        $stmt->execute();

        $count = $stmt->fetchColumn();
        $num_pages = ceil($count / $perPage);
        return $num_pages;
    }

    public function HomeFeedPosts(int $perPage, string $nickname) {

        $page = test_input((int) ($_GET['page'] ?? 1));
        $offset = ($page-1)*$perPage;

        $stmt = $this->pdo->prepare('SELECT posts.posts_id, posts.user_nickname, posts.title, posts.content, posts.image_folder, 
                            posts.likes, posts.comments, DATE_FORMAT(posts.`time`, "%Y-%m-%dT%H:%i:%s") AS `time`,
                            GROUP_CONCAT(percategory.category_title ORDER BY percategory.category_title SEPARATOR ", ") AS categories
                            FROM posts
                            INNER JOIN follows ON follows.user_nickname = posts.user_nickname
                            LEFT JOIN percategory ON percategory.post_id = posts.posts_id
                            WHERE deleted = :deleted AND follows.follower = :follower AND follows.status = 1
                            GROUP BY posts.posts_id
                            ORDER BY posts.posts_id DESC 
                            LIMIT :perPage OFFSET :offset');

        $stmt->bindValue(':deleted', false , PDO::PARAM_BOOL);
        $stmt->bindValue(':follower', $nickname);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $entries;
    }

    public function HomeFeedPosts_pages(int $perPage, string $nickname) {

        $page = test_input((int) ($_GET['page'] ?? 1));
        $offset = ($page-1)*$perPage;

        $stmt = $this->pdo->prepare('SELECT COUNT(*) AS `count`
                            FROM posts
                            INNER JOIN follows ON follows.user_nickname = posts.user_nickname
                            LEFT JOIN percategory ON percategory.post_id = posts.posts_id
                            WHERE deleted = :deleted AND follows.follower = :follower AND follows.status = 1
                            GROUP BY posts.posts_id
                            ORDER BY posts.posts_id DESC 
                            LIMIT :perPage OFFSET :offset');

        $stmt->bindValue(':deleted', false , PDO::PARAM_BOOL);
        $stmt->bindValue(':follower', $nickname);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $count = $stmt->fetchColumn();
        $num_pages = ceil($count / $perPage);
        
        return $num_pages;
    }
}