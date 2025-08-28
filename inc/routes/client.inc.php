<?php 
if ($pages == 'browse' || $pages == 'main') {
        $perPage = 10;
        $entriesController = $container->get('entriesController');
        if ($status === true){
            $entriesController->browse($perPage, $status, $isadmin, $_SESSION['nickname']);
        }
        else {
            $entriesController->browse($perPage, $status, $isadmin, false);
        }
    }
    elseif($pages === 'myart'){
        $perPage = 10;
        $entriesController = $container->get('entriesController');
        $entriesController->myArt($perPage, $status, $isadmin);
    }
    elseif($pages === 'contact_us'){
        $clientController = $container->get('clientController');
        $clientController->render_r('contact_us', ['status' => $status, 'isadmin' => $isadmin]);
    }
    elseif ($pages === 'search'){
        $perPage = 10;
        $quote = (string) ($_GET['quote'] ?? '');
        $entriesController = $container->get('entriesController');
        if ($status === true){
            $entriesController->search($perPage, $status, $isadmin, $quote, $_SESSION['nickname']);
        }
        else {
            $entriesController->search($perPage, $status, $isadmin, $quote, false);
        }
    }
    elseif ($pages === 'search_user'){
        $perPage = 10;
        $quote = (string) ($_GET['search_q'] ?? '');
        $usersController = $container->get('usersController');
        $usersController->search_user($perPage, $status, $isadmin, $quote);    
    }
    elseif ($pages === '4u'){
        
    }
    elseif ($pages === 'my_home') {
        if ($status === true){
            $perPage = 10;
            $entriesController = $container->get('entriesController');
            $entriesController->myHomeFeed($perPage, $status, $isadmin, $_SESSION['nickname']);
        }
        else {
            header("Location: index.php?" . http_build_query(['route' => 'admin' , 'pages' => 'login']));
        }
    }
    elseif ($pages === 'category') {
        $perPage = 10;
        $category_name = (string) ($_GET['category'] ?? '');
        $categoriesRepository = $container->get('categoriesRepository');
        $category = '';
        $category = $categoriesRepository->categoryInfo($category_name);
        if (empty($category)){
            header("Location: index.php?" . http_build_query(['route' => 'client' , 'pages' => 'notFound']));
        }
        $entriesController = $container->get('entriesController');
        if ($status === true){
            $entriesController->browsePerCategory($perPage, $status, $isadmin, $_SESSION['nickname']);
        }
        else {
            $entriesController->browsePerCategory($perPage, $status, $isadmin, false);
        }
    }
    elseif ($pages === 'categories'){
        $perPage = 40;
        $categoriesController = $container->get('categoriesController');
        $categoriesController->showCategories($perPage, $status, $isadmin);
    }
    elseif( $pages === 'categories_search') {
        $perPage = 40;
        $quote = (string) ($_GET['search_q'] ?? '');
        $categoriesController = $container->get('categoriesController');
        $categoriesController->searchCategories($perPage, $status, $isadmin, $quote);
    }
    elseif ( $pages === 'post') {
        $post_id = (int) ($_GET['post_id'] ?? '');
        $entriesRepository = $container->get('entriesRepository');
        $post = '';
        $post = $entriesRepository->postByID($post_id);
        if (empty($post)){
            header("Location: index.php?" . http_build_query(['route' => 'client' , 'pages' => 'notFound']));
        }
        $entriesController = $container->get('entriesController');

        // making sure the user isnt blocked by the posts owner
        $post_owner = $entriesRepository->owner_by_postID($post_id);
        $usersRepository = $container->get('usersRepository');
        $blocked = $usersRepository->isblocked($_SESSION['nickname'], $post_owner);

        if ($status === true){
            if(($entriesRepository->isowner($post_id) && !($entriesRepository->deleted_status($post_id))) || $isadmin === true){

                $entriesController->showPost($post_id, $status, $isadmin, $entriesRepository->isowner($post_id));
            }
            elseif (!($entriesRepository->deleted_status($post_id)) && $entriesRepository->is_public($post_id) && ($blocked != 1)) {
                $entriesController->showPost($post_id, $status, $isadmin, false);
            }
            elseif (!($entriesRepository->deleted_status($post_id)) && $entriesRepository->is_public($post_id) && ($blocked == 1)){
                $entriesController->showBlockedPost($post_id, $status, $isadmin);
            }
            else {
                header("Location: index.php?" . http_build_query(['route' => 'client' , 'pages' => 'notFound']));
            }
        }
        elseif (!($entriesRepository->deleted_status($post_id)) && $entriesRepository->is_public($post_id)){
            $entriesController->showPost($post_id, $status, $isadmin, false);
        }
        else {
            header("Location: index.php?" . http_build_query(['route' => 'client' , 'pages' => 'notFound']));
        }
    }
    elseif ( $pages === 'create') {
        if($status === true){
            $categoriesRepository = $container->get('categoriesRepository');
            $allcategories = $categoriesRepository->allCategories();
            $entriesController = $container->get('entriesController');
            $entriesController->createPost($status, $isadmin, $allcategories);
        }
        else {
            header("Location: index.php?" . http_build_query(['route' => 'admin' , 'pages' => 'login']));
        }
    }
    elseif ($pages === 'edit/post'){
        $post_id = (string) ($_GET['post_id'] ?? '');
        $entriesRepository = $container->get('entriesRepository');
        $post = '';
        $post = $entriesRepository->postByID($post_id);
        if (empty($post)){
            header("Location: index.php?" . http_build_query(['route' => 'client' , 'pages' => 'notFound']));
        }
        $entriesController = $container->get('entriesController');
        if ($status === true){
            if($entriesRepository->isowner($post_id)){
                $categoriesRepository = $container->get('categoriesRepository');
                $allcategories = $categoriesRepository->allCategories();
                $entriesController->editPost((int) $post_id, $status, $isadmin, $allcategories, true);
            }
            elseif ($isadmin === true){
                header("Location: index.php?" . http_build_query(['route' => 'admin' , 'pages' => 'edit/post', 'post_id' => $post_id]));
            }
            else {
                header("Location: index.php?" . http_build_query(['route' => 'client' , 'pages' => 'post', 'post_id' => $post_id]));
            }
        }
    }
    elseif( $pages === 'delete/post'){
        $entriesController = $container->get('entriesController');
        $entriesRepository = $container->get('entriesRepository');
        $post_id = (int) ($_GET['post_id'] ?? '');
        $post = '';
        $post = $entriesRepository->postByID($post_id);
        if (empty($post)){
            header("Location: index.php?" . http_build_query(['route' => 'client' , 'pages' => 'notFound']));
        }
        if ($status === true){
            if($entriesRepository->isowner($post_id) || ($isadmin === true)){
                $entriesRepository->clientDeletePost((int) $post_id);
                header("Location: index.php?" . http_build_query(['route' => 'client' , 'pages' => 'browse']));
            }
        }
    }
    elseif($pages === 'profile'){
        $nickname = (string) ($_GET['nickname'] ?? '');
        $user = '';
        $usersRepository = $container->get('usersRepository');
        $user = $usersRepository->user($nickname);
        if (empty($user)){
            header("Location: index.php?" . http_build_query(['route' => 'client' , 'pages' => 'notFound']));
        }
        $entriesRepository = $container->get('entriesRepository');
        $clientController = $container->get('clientController');
        $usersRepository = $container->get('usersRepository');
        $post_status = 'public';
        if ($status === true){
            if ($isadmin === true) {
                $entries = $entriesRepository->allEntriesByNickname($nickname, 10);
                $page_num = $entriesRepository->allEntriesByNickname_num($nickname, 10);
                $managerController = $container->get('managerController');
                $managerController->showProfile($entries, $user, true, $usersRepository->profileOwner($nickname), true, $page_num);
            }
            else {
                $blocked = $usersRepository->isblocked($_SESSION['nickname'], $nickname);
                //var_dump($blocked);
                if ($blocked != 1){
                    $entries = $entriesRepository->showEntriesByNickname_n_Deleted_n_PublicStatus($nickname, false, 'public', 10);
                    $page_num = $entriesRepository->showEntriesByNickname_n_Deleted_n_PublicStatus_num($nickname, false, 'public', 10);
                    $clientController->showProfile($entries, $user, $isadmin, $usersRepository->profileOwner($nickname), true , $page_num, $post_status);
                }
                else {
                    $clientController->showBlockedProfile($nickname, true, $isadmin);
                }
            }
        }
        else {
            $entries = $entriesRepository->showEntriesByNickname_n_Deleted_n_PublicStatus($nickname, false, 'public', 10);
            $page_num = $entriesRepository->showEntriesByNickname_n_Deleted_n_PublicStatus_num($nickname, false, 'public', 10);
            $clientController->showProfile($entries, $user, false, false, false, $page_num, $post_status);
        }
    }
    elseif($pages === 'profile/private'){
        $nickname = (string) ($_GET['nickname'] ?? '');
        $user = '';
        $usersRepository = $container->get('usersRepository');
        $user = $usersRepository->user($nickname);
        if (empty($user)){
            header("Location: index.php?" . http_build_query(['route' => 'client' , 'pages' => 'notFound']));
        }
        $entriesRepository = $container->get('entriesRepository');
        $clientController = $container->get('clientController');
        if ($status === true){
            if ($isadmin === true || $usersRepository->profileOwner($nickname)) {
                $post_status = 'private';
                $entries = $entriesRepository->showEntriesByNickname_n_Deleted_n_PublicStatus($nickname, false, 'private', 10);
                $page_num = $entriesRepository->showEntriesByNickname_n_Deleted_n_PublicStatus_num($nickname, false, 'private', 10);
                $clientController->showProfile($entries, $user, $isadmin, $usersRepository->profileOwner($nickname), true , $page_num, $post_status);
            }
            else {
                header("Location: index.php?" . http_build_query(['route' => 'client' , 'pages' => 'notFound']));
            }
        }
        else {
            header("Location: index.php?" . http_build_query(['route' => 'admin' , 'pages' => 'login']));
        }
    }
    elseif($pages === 'edit/profile'){
        $nickname = (string) ($_GET['nickname'] ?? '');
        $user = '';
        $usersRepository = $container->get('usersRepository');
        $user = $usersRepository->user($nickname);
        if (empty($user)){
            header("Location: index.php?" . http_build_query(['route' => 'client' , 'pages' => 'notFound']));
        }
        if ($status === true) {
            if ($isadmin === true || $usersRepository->profileOwner($nickname)){
                $usersController = $container->get('usersController');
                $usersController->editUser($nickname, $isadmin, $status);
            }
            else {
                echo "this: " . $usersRepository->profileOwner($nickname) . " is " . $nickname;
                die();
                header("Location: index.php?" . http_build_query(['route' => 'client' , 'pages' => 'notFound']));
            }
        }
        else {
            header("Location: index.php?" . http_build_query(['route' => 'admin' , 'pages' => 'login']));
        }
    }
    elseif ($pages === 'liked_posts'){
        if ($status === true){
            $perPage = 40;
            $entriesController = $container->get('entriesController');
            $entriesController->showLikedPosts($perPage, $_SESSION['usersID'], $isadmin, $status);
        }
        else {
            header("Location: index.php?" . http_build_query(['route' => 'admin' , 'pages' => 'login']));
        }
    }
    elseif ($pages === 'followers'){
        if ($status === true){
            $perPage = 40;
            $usersController = $container->get('usersController');
            $usersController->ShowFollowers($perPage, $status, $isadmin, $_SESSION['nickname']);
        }
        else {
            header("Location: index.php?" . http_build_query(['route' => 'admin' , 'pages' => 'login']));
        }
    }
    elseif ($pages === 'following'){
        if ($status === true){
            $perPage = 40;
            $usersController = $container->get('usersController');
            $usersController->ShowFollowing($perPage, $status, $isadmin, $_SESSION['nickname']);
        }
        else {
            header("Location: index.php?" . http_build_query(['route' => 'admin' , 'pages' => 'login']));
        }
    }
    else {
        $notFoundController = $container->get('notFoundController');
        $notFoundController->error404();
    }