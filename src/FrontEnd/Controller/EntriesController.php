<?php

namespace App\FrontEnd\Controller;

use App\Repository\EntriesRepository;
use PDO;

class EntriesController extends ClientController {

    public function __construct(protected EntriesRepository $entriesRepository){}

    public function browse(int $perPage, bool $status, bool $isadmin, string|bool $nickname) {
        
        $excludedUsers = $this->entriesRepository->excludedUsers($nickname);
        
        $entries = $this->entriesRepository->browse($perPage, $excludedUsers);
        $num_pages = $this->entriesRepository->num_Pages_browse($perPage, $excludedUsers);
        $page = test_input((int)($_GET['page'] ?? 1));

        $art_images = $this->entriesRepository->artBanner();

        $this->render('main', [
            'entries'=> $entries,
            'page' => $page,
            'num_pages' => $num_pages,
            'status' => $status,
            'isadmin' => $isadmin,
            'art_images' => $art_images
        ]);
    }

    public function browsePerCategory(int $perPage, bool $status, bool $isadmin, string|bool $nickname) {
        
        $excludedUsers = $this->entriesRepository->excludedUsers($nickname);

        $entries = $this->entriesRepository->browsePerCategory($perPage, $excludedUsers);
        $num_pages = $this->entriesRepository->num_pages_per_cat($perPage, $excludedUsers);

        $this->render('category', [
            'entries' => $entries,
            'page' => 1,
            'num_pages' => $num_pages,
            'status' => $status,
            'isadmin' => $isadmin
        ]);

    }

    public function showPost(int $post_id, bool $status, bool $isadmin, bool $isowner) {

        $post = $this->entriesRepository->postByID($post_id);

        $this->render('post', [
            'post' => $post,
            'status' => $status,
            'isadmin' => $isadmin,
            'isowner' => $isowner,
        ]);
        
    }

    public function showBlockedPost(int $post_id, bool $status, bool $isadmin){
        
        $this->render('post.blocked', [
            'status' => $status,
            'isadmin' => $isadmin,
            'isowner' => false,
            'post_id' => $post_id
        ]);
    }

    public function createPost(bool $status, bool $isadmin, $allcategories) {

        $user_id = (int) $_SESSION['usersID'];
        $nickname = (string) $_SESSION['nickname'];

        $errors = [];

        if (!empty($_POST)){

            $title = @(string) test_input($_POST['title'] ?? '');
            $description = @(string) test_input($_POST['description'] ?? '');
            $categories = json_decode($_POST['categories'], true);
            $post_status = @(string) test_input($_POST['post_status']);
            $type = @(string) test_input($_POST['post_type']);

            $all_cats = [];
            foreach ($allcategories AS $allcategory){
                $all_cats [] = $allcategory['title'];
            }

            $final_categories = [];
    
            foreach ($categories as $category) {

                $category = ucfirst(strtolower($category));
                if (in_array($category, $all_cats)) {
                    $final_categories[] = ucfirst(strtolower($category));
                } 
                else {
                    try {
                        $pdo = require __DIR__ . '/../../../inc/db-connect.inc.php';
                        $category = ucfirst(strtolower($category));
                        $stmt = $pdo->prepare('INSERT INTO `categories` (`title`, `description`) VALUES (:title, :description)');
                        $stmt->bindValue(':title', $category);
                        $stmt->bindValue(':description', ' ');
            
                        try {
                            $stmt->execute();
                        } 
                        catch (PDOException $e) {
                            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                                // already exists, skip insert
                            } 
                            else {
                                throw $e;
                            }
                        }
            
                        $final_categories[] = ucfirst(strtolower($category));
            
                    } 
                    catch (PDOException $e) {
                        error_log('Database error: ' . $e->getMessage());
                        echo 'Database error occurred.';
                        die();
                    }
                }
            }

            if (empty($final_categories)){
                $final_categories [] = 'none';
            }
            
            if($post_status !== 'public' && $post_status !=='private'){
                $post_status = 'private';
            }

            if ( !empty($_FILES) && !empty($_FILES['image']) ){
                $image = $_FILES['image'];
            }
            else {
                $errors []= "no image submitted";
            }

            if(!empty($title) && !empty($description) && !empty($image)){
                
                $imageSubmit = $this->entriesRepository->imageProcessing($image, $user_id);
 
                if (!empty($imageSubmit)){
                    $createdOk = $this->entriesRepository->finalizing_posting($imageSubmit, $user_id, $nickname, $title, $description, $final_categories, $post_status, $type);

                    if ($createdOk !== false){
                        header("Location: index.php?" . http_build_query(['route' => 'client' , 'pages' => 'main']));
                        exit;
                    }
                    else {
                        $errors [] = "entry couldnt be created";
                    }

                }
                else {
                    $errors []= "something is wrong with your image, submit a different one";
                }

            }
            else {
                $errors [] = "not everything is filled";
            }
            
        }

        $this->render('create', [
            'errors' => $errors,
            'status' => $status,
            'isadmin' => $isadmin,
            'categories' => $allcategories
        ]);
    }
    
    public function editPost(int $post_id, bool $status, bool $isadmin, $allcategories, bool $isowner){

        $errors = [];
        $user_id = (int) $_SESSION['usersID'];

        $entry = $this->entriesRepository->postByID((int)$post_id);
        $categories = $this->entriesRepository->catByPostId((int)$post_id);

        if(!empty($_POST)){
            $title = @(string) test_input(($_POST['title'] ?? ''));
            $description = @(string) test_input(($_POST['description'] ?? ''));
            $categories = json_decode($_POST['categories'] ?? '[]', true);
            $post_status = @(string) test_input(($_POST['post_status']));

            $image = [];
            
            $all_cats = [];
            foreach ($allcategories AS $allcategory){
                $all_cats [] = ucfirst(strtolower($allcategory['title']));
            }

            $final_categories = [];
    
            foreach ($categories as $category) {
                $category = ucfirst(strtolower($category));
                if (in_array($category, $all_cats)) {
                    $final_categories[] = ucfirst(strtolower($category));
                } 
                else {
                    try {
                        $pdo = require __DIR__ . '/../../../inc/db-connect.inc.php';
                        $category = ucfirst(strtolower($category));
                        $stmt = $pdo->prepare('INSERT INTO `categories` (`title`, `description`) VALUES (:title, :description)');
                        $stmt->bindValue(':title', $category);
                        $stmt->bindValue(':description', ' ');
            
                        try {
                            $stmt->execute();
                        } 
                        catch (PDOException $e) {
                            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                                // Already exists, skip insert
                            } 
                            else {
                                throw $e;
                            }
                        }
            
                        $final_categories[] = ucfirst(strtolower($category));
            
                    } catch (PDOException $e) {
                        error_log('Database error: ' . $e->getMessage());
                        echo 'Database error occurred.';
                        die();
                    }
                }
            }

            if (empty($final_categories)){
                $final_categories [] = 'None';
            }
            
            if($post_status !== 'public' && $post_status !=='private'){
                $post_status = 'private';
            }
            
            if ( !empty($_FILES) && !empty($_FILES['image']) ){
                $image = $_FILES['image'];
            }

            //echo "title " . $title . "desc " . $description . "<br>";
            if(!empty($title) && !empty($description)){
                //echo "criteria met";
                if (!empty($image['name'])){
                    $imageSubmit = $this->entriesRepository->imageProcessing($image, $user_id);
                    echo "i proccessed the image";
                    if (!empty($imageSubmit)){
                        //echo "image okay";
                        $updatedOk = $this->entriesRepository->update_posting($imageSubmit, $post_id, $title, $description, $final_categories, $post_status);
                    }
                    else {
                        $errors []= "something is wrong with your image, submit a different one";
                    }
                }
                else {
                    //echo "i have no new image";
                    $imageSubmit = false;
                    $updatedOk = $this->entriesRepository->update_posting($imageSubmit, $post_id, $title, $description, $final_categories, $post_status);
                }

                if ($updatedOk !== false && empty($errors)){
                    header("Location: index.php?" . http_build_query(['route' => 'client' , 'pages' => 'post', 'post_id' => $post_id]));
                    exit;
                }
                else {
                    $errors [] = "post couldnt be updated";
                }

            }
            else {
                $errors [] = "not everything is filled out";
            }

        }

        $this->render('edit.post', [
            'entry' => $entry,
            'errors' => $errors,
            'status' => $status,
            'isadmin' => $isadmin,
            'isowner' => $isowner,
            'allcategories' => $allcategories,
            'categories' => $categories
        ]);
    }

    public function search(int $perPage, bool $status, bool $isadmin, string $quote, string|bool $nickname){
        $errors = [];
        $page = test_input((int)($_GET['page'] ?? 1));
        $quote = test_input(($_GET['search_q'] ?? ''));

        $excludedUsers = $this->entriesRepository->excludedUsers($nickname);

        $entries = $this->entriesRepository->postsByQuote($perPage, $quote, $excludedUsers);
        $num_pages = $this->entriesRepository->num_Pages_quote($perPage, $quote, $excludedUsers);

        $this->render('search', [
            'entries' => $entries,
            'errors' => $errors,
            'status' => $status,
            'isadmin' => $isadmin,
            'page' => $page,
            'num_pages' => $num_pages
        ]);
        
    }

    public function postByIDAPI(int $post_id){
        
        $post = $this->entriesRepository->postByID($post_id);
        $image_location = include __DIR__ . '/../../Variables/image_location.php';
        $post['image_folder'] = $image_location . $post['image_folder'];

        if (!empty($post)) {
            return $this->json($post, 200);
        } 
        else {
            return $this->json(['error' => 'Post not found'], 404);
        }
    }

    public function postsByNicknameAPI(string $nickname){

        $posts = $this->entriesRepository->showEntriesByNickname_n_Deleted_n_PublicStatusAPI($nickname);
        $image_location = include __DIR__ . '/../../Variables/image_location.php';

        foreach ($posts AS &$post){
            $post['image_folder'] = $image_location . $post['image_folder'];
        }
        unset($post);

        if (!empty($posts)) {
            return $this->json($posts, 200);
        } 
        else {
            return $this->json(['error' => 'Post not found'], 404);
        } 
    }

    public function myArt(int $perPage, bool $status, bool $isadmin) {
        
        $errors = [];
        $page = test_input((int)($_GET['page'] ?? 1));

        $entries = $this->entriesRepository->art($perPage, 'Anna');
        $num_pages = $this->entriesRepository->num_art($perPage, 'Anna');

        $this->render('my.art', [
            'entries' => $entries,
            'errors' => $errors,
            'status' => $status,
            'isadmin' => $isadmin,
            'page' => $page,
            'num_pages' => $num_pages
        ]);
    }

    public function showLikedPosts(int $perPage, string $users_id, bool $isadmin, bool $status) {

        $errors = [];
        $page = test_input((int)($_GET['page'] ?? 1));

        $entries = $this->entriesRepository->LikedPosts($perPage, $users_id);
        $num_pages = $this->entriesRepository->LikedPosts_pages($perPage, $users_id);

        $this->render('liked.posts', [
            'entries' => $entries,
            'errors' => $errors,
            'status' => $status,
            'isadmin' => $isadmin,
            'page' => $page,
            'num_pages' => $num_pages
        ]);
    }

    public function myHomeFeed(int $perPage, bool $status, bool $isadmin, string $nickname){

        $errors = [];
        $page = test_input((int)($_GET['page'] ?? 1));

        $entries = $this->entriesRepository->HomeFeedPosts($perPage, $nickname);
        $num_pages = $this->entriesRepository->HomeFeedPosts_pages($perPage, $nickname);

        $art_images = $this->entriesRepository->artBanner();

        $this->render('my_home', [
            'entries' => $entries,
            'errors' => $errors,
            'status' => $status,
            'isadmin' => $isadmin,
            'page' => $page,
            'num_pages' => $num_pages,
            'art_images' => $art_images
        ]);
    }
    
}