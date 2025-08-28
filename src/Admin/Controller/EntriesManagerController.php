<?php

namespace App\Admin\Controller;

use App\Repository\EntriesRepository;

class EntriesManagerController extends ManagerController{

    public function __construct(protected EntriesRepository $entriesRepository){}

    public function viewCategory(int $perPage){

        $entries = $this->entriesRepository->browsePerCategory($perPage);
        $num_pages = $this->entriesRepository->num_pages_per_cat($perPage);

        $this->render_admin('category', [
            'entries' => $entries,
            'num_pages' => $num_pages,
            'page' => 1
        ]);        
    }

    public function showPost(int $post_id){

        $post = $this->entriesRepository->postByID($post_id);

        $this->render_admin('post', [
            'post' => $post
        ]);
    }

    public function editPost(int $post_id, $allcategories){
        $errors = [];

        $entry = $this->entriesRepository->postByID((int)$post_id);
        $nickname = $entry['user_nickname'];
        $user = $this->entriesRepository->userByNickname($nickname);
        $user_id = $user->users_id;

        $categories = $this->entriesRepository->catByPostId((int)$post_id);

        if(!empty($_POST)){
            
            $title = @(string) test_input(($_POST['title'] ?? ''));
            $description = @(string) test_input(($_POST['description'] ?? ''));
            $categories = json_decode($_POST['categories'] ?? '[]', true);
            $post_status = @(string) test_input(($_POST['post_status']));
            $image = [];

            //categories handling
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
                        } catch (PDOException $e) {
                            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                                // already exists, skip insert
                            } else {
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
                $final_categories [] = 'none';
            }

            if ( !empty($_FILES) && !empty($_FILES['image']) ){
                $image = $_FILES['image'];
            }
            
            if(!empty($title) && !empty($description)){
                //echo "criteria met";
                if (!empty($image['name'])){
                    $imageSubmit = $this->entriesRepository->imageProcessing($image, $user_id);
                    //echo "i proccessed the image";
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
                if (empty($errors) && $updatedOk !== false){
                    header("Location: index.php?" . http_build_query(['route' => 'client' , 'pages' => 'post', 'post_id' => $post_id]));
                    exit;
                }
                else {
                    $errors [] = "post couldnt be updated";
                }

            }

        }

        $this->render_admin('edit.post', [
            'entry' => $entry,
            'errors' => $errors,
            'status' => true,
            'isadmin' => true,
            'allcategories' => $allcategories,
            'categories' => $categories
        ]);
    }

    public function deletedPosts(int $perPage){

        $entries = $this->entriesRepository->deletedPosts($perPage);
        $num_pages = $this->entriesRepository->deletedPosts_num($perPage);

        $this->render_admin('deleted.posts', [
            'entries' => $entries,
            'status' => true,
            'isadmin' => true,
            'num_pages' => $num_pages,
            'page' => 1
        ]);
    }
}