<?php

namespace App\Admin\Controller;

use App\Repository\CategoriesRepository;

class CategoriesManagerController extends ManagerController {

    public function __construct(protected CategoriesRepository $categoriesRepository){}

    public function editCategories(int $perPage){
        
        $categories = $this->categoriesRepository->showCategories($perPage);
        $num_pages = $this->categoriesRepository->catPages($perPage);

        $this->render_admin('categories', [
            'categories' => $categories,
            'num_pages' => $num_pages,
            'page' => 1
        ]);
    }

    public function editCategory(string $category) {
        
        $category = $this->categoriesRepository->categoryInfo($category);
        $error = [];

        if(!empty($_POST)){
            
            $new_title = @(string) test_input(($_POST['title'] ?? ''));
            $description = @(string) test_input(($_POST['description'] ?? ''));
            $category_id = @(int) test_input(($_POST['cat_id'] ?? $category->category_id));

            if(!empty($new_title) && !empty($description)){
                $new_title_exists = $this->categoriesRepository->categoryInfo($new_title);
                $new_category_id = $new_title_exists->category_id;

                if ($new_title_exists !== false){
                    if ($new_category_id === $category_id){
                        $this->categoriesRepository->update($new_title, $description, $category_id);
                        header("Location: index.php?". http_build_query(['route' => 'admin' , 'pages' => 'edit/categories']));
                        exit;
                    }
                    $error [] = 'title already exists';
                }
                else {
                    $this->categoriesRepository->update($new_title, $description, $category_id);
                    header("Location: index.php?". http_build_query(['route' => 'admin' , 'pages' => 'edit/categories']));
                    exit;
                }
            }
            else {
                $error [] = "Are all the fields filled out?";
            }
        }

        $this->render_admin('edit.category', [
            'category' => $category,
            'error' => $error
        ]);
    }

    public function viewCategory(){

        $category_title = @(string) test_input(($_GET['category'] ?? ''));
        $category = $this->categoriesRepository->categoryInfo($category_title);

        $this->render_admin('category', [
            'category' => $category,
        ]);        
    }

    public function createCategory(){
        $error = [];

        if(!empty($_POST)){
            
            $title = @(string) test_input(($_POST['title'] ?? ''));
            $description = @(string) test_input(($_POST['description'] ?? ''));

            if(!empty($title)){
                $title_exists = $this->categoriesRepository->categoryInfo($title);

                if ($title_exists !== false){
                    $error [] = 'title already exists';
                }
                else {
                    $title = ucfirst(strtolower($title));
                    $this->categoriesRepository->create($title, $description);
                    header("Location: index.php?". http_build_query(['route' => 'admin' , 'pages' => 'edit/categories']));
                    exit;
                }
            }
            else {
                $error [] = "title doesnt exist";
            }
        }

        $this->render_admin('create.category', [
            'error' => $error
        ]);

    }

    public function deleteCategory(string $category){

        $category = $this->categoriesRepository->categoryInfo($category);
        $category_title = $category->title;

        //$this->categoriesRepository->deletePerCatwithCatTitle($category_title);
        $this->categoriesRepository->deleteCategory($category_title);

        header("Location: index.php?". http_build_query(['route' => 'admin' , 'pages' => 'edit/categories']));

    }
}