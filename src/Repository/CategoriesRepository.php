<?php

namespace App\Repository;

use PDO;
use App\Model\CategoryModel;

class CategoriesRepository {

    public function __construct(private PDO $pdo){}

    public function catNumber(){
        $stmtCount = $this->pdo->prepare('SELECT COUNT(*) AS `count` FROM `categories`');
        $stmtCount->execute();
        $count = $stmtCount->fetch(PDO::FETCH_ASSOC)['count'];
        return $count;
    }

    public function catPages(int $perPage) : int {
        $count = $this->catNumber();
        $num_pages = ceil($count / $perPage);
        return $num_pages;
    }

    public function showCategories(int $perPage) : array {

        $page = test_input((int) ($_GET['page'] ?? 1));
        $offset = ($page-1)*$perPage;

        $stmt = $this->pdo->prepare('SELECT * FROM `categories` ORDER BY `title` ASC LIMIT :perPage OFFSET :offset');
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function categoryInfo(string $category) : bool | CategoryModel {

        $stmt = $this->pdo->prepare('SELECT * FROM `categories` WHERE `title`=:title');
        $stmt->bindValue(':title', $category);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, CategoryModel::class);
        $category = $stmt->fetch();
        return $category;
    }

    public function update(string $new_title, string $description, int $category_id) : bool {

        $stmt = $this->pdo->prepare('UPDATE `categories` SET `title`= :title, `description`= :description WHERE `category_id`= :category_id ');
        $stmt->bindValue(':title', $new_title);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function allCategories(){
        $stmt = $this->pdo->prepare('SELECT `title` FROM `categories`');
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    public function create(string $title, string $description){
        $stmt = $this->pdo->prepare('INSERT INTO `categories` ( `title`, `description`) 
                                    VALUES ( :title, :description )');
        $stmt->bindValue(':title', $title);                          
        $stmt->bindValue(':description', $description);
        return $stmt->execute();
    }

    public function deleteCategory(string $title){
        $stmt = $this->pdo->prepare('DELETE FROM categories WHERE `title`=:title');
        $stmt->bindValue(':title', $title);
        return $stmt->execute();
    }

    public function showCategoriesWithQuote(int $perPage, string $quote) {

        $page = test_input((int) ($_GET['page'] ?? 1));
        $offset = ($page-1)*$perPage;
        $like = '%' . $quote . '%';

        $stmt = $this->pdo->prepare('SELECT *
                    FROM `categories`
                    WHERE `title` LIKE :quote
                    ORDER BY `title` ASC 
                    LIMIT :perPage OFFSET :offset');

        $stmt->bindValue(':quote', $like);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $categories;
    }

    public function catPagesWithQuote(int $perPage, string $quote) {

        $like = '%' . $quote . '%';

        $stmt = $this->pdo->prepare('SELECT COUNT(DISTINCT `title`)
                    FROM categories
                    WHERE `title` LIKE :quote');

        $stmt->bindValue(':quote', $like);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $page_num = ceil($count/$perPage); 
    }
}