<?php

namespace App\FrontEnd\Controller;

use App\Repository\CategoriesRepository;

class CategoriesController extends ClientController {

    public function __construct(protected CategoriesRepository $categoriesRepository){}
    
    public function showCategories(int $perPage, bool $status, bool $isadmin){

        $categories = $this->categoriesRepository->showCategories($perPage);
        $num_pages = $this->categoriesRepository->catPages($perPage);
        $page = (int)($_GET['page'] ?? 1);
        $this->render('categories', [
            'cats' => $categories,
            'page' => $page,
            'num_pages' => $num_pages,
            'status' => $status,
            'isadmin' => $isadmin
        ]);      
    }

    public function searchCategories(int $perPage, bool $status, bool $isadmin, string $quote) {

        $categories = $this->categoriesRepository->showCategoriesWithQuote($perPage, $quote);
        $num_pages = $this->categoriesRepository->catPagesWithQuote($perPage, $quote);
        $page = (int)($_GET['page'] ?? 1);

        $this->render('categories', [
            'cats' => $categories,
            'page' => $page,
            'num_pages' => $num_pages,
            'status' => $status,
            'isadmin' => $isadmin
        ]); 
    }

}