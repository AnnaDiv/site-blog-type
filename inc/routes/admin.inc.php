<?php
if ($pages === 'login'){
        $loginController = $container->get('loginController');
        $loginController->login($status, $isadmin);
    }
    else if ($pages === 'logout'){
        $loginController = $container->get('loginController');
        $loginController->logout();
    }
    elseif($pages === 'create/login'){
        $usersManagerController = $container->get('usersManagerController');
        $usersManagerController->createUser($status, $isadmin);
    }
    elseif($pages === 'email/sent/acc'){
        $managerController = $container->get('managerController');
        $managerController->showEmailSentAcc($status, $isadmin);
    }
    elseif($pages === 'help'){
        $managerController = $container->get('managerController');
        $managerController->showHelp($status, $isadmin);
    }
    elseif($pages === 'help/login'){
        $phpMailerController = $container->get('PHPMailerController');
        $phpMailerController->resetPassword($status, $isadmin);
    }
    elseif($pages === 'email/sent'){
        $managerController = $container->get('managerController');
        $managerController->showEmailSent($status, $isadmin);
    }
    elseif ($pages === 'request_pass'){
        $requestID = (string) ($_GET['requestID'] ?? '');
        $requestsController = $container->get('requestsController');
        $requestsController->passwordChange($requestID);
    }
    elseif ($pages === 'request_acc'){
        $requestID = (string) ($_GET['requestID'] ?? '');
        $requestsController = $container->get('requestsController');
        $requestsController->activateAccount($requestID);
    }
    else {
        if ($authService->isAdmin()){
            if($pages === 'control') {
                $managerController = $container->get('managerController');
                $managerController->showPanel($status, $isadmin);
            }
            elseif ($pages === 'create/art'){
                $entriesController = $container->get('entriesController');
                $entriesController->createArt($status, $isadmin);
            }
            elseif ($pages === 'xmlfeed/browse'){
                header('Content-Type: text/html; charset=utf-8');

                $xmlFile = 'http://localhost/blog-type-site/src/XMLFeed/XMLFeed_browse.php';
                $xslFile = 'http://localhost/blog-type-site/src/XMLFeed/feed-style-browse.xsl';

                echo $res = load_xml($xmlFile, $xslFile);
            }
            elseif ($pages === 'xmlfeed/user'){
                header('Content-Type: text/html; charset=utf-8');

                $nickname = ($_GET['nickname'] ?? 'Anna');
                $xmlFile = 'http://localhost/blog-type-site/src/XMLFeed/XMLFeed_user.php?nickname='. $nickname;
                $xslFile = 'http://localhost/blog-type-site/src/XMLFeed/feed-style-user.xsl';

                echo $res = load_xml($xmlFile, $xslFile);
            }
            elseif($pages === 'edit/categories') {
                $perPage = 10;
                $categoriesManagerController = $container->get('categoriesManagerController');
                $categoriesManagerController->editCategories($perPage);
            }
            elseif($pages === 'view/category') {
                $perPage = 10;
                $categoriesManagerController = $container->get('categoriesManagerController');
                $categoriesManagerController->viewCategory($perPage);
            }
            elseif($pages === 'edit/category') {
                $category_name = (string) ($_GET['category'] ?? '');
                $categoriesManagerController = $container->get('categoriesManagerController');
                $categoriesManagerController->editCategory($category_name);
            }
            elseif($pages === 'view/post'){
                $post_id = (string) ($_GET['post_id'] ?? '');
                $usersRepository = $container->get('usersRepository');
                $entriesManagerController = $container->get('entriesManagerController');
                $entriesManagerController->showPost($post_id);
            }
            elseif($pages === 'create/category'){
                $categoriesManagerController = $container->get('categoriesManagerController');
                $categoriesManagerController->createCategory();  
            }
            elseif($pages === 'delete/category'){
                $category_name = (string) ($_GET['category'] ?? '');
                $categoriesManagerController = $container->get('categoriesManagerController');
                $categoriesManagerController->deleteCategory($category_name); 
            }
            elseif($pages === 'browse/pages'){

            }
            elseif($pages === 'edit/post'){
                $post_id = (string) ($_GET['post_id'] ?? '');
                $entriesManagerController = $container->get('entriesManagerController');
                $categoriesRepository = $container->get('categoriesRepository');
                $allcategories = $categoriesRepository->allCategories();
                $entriesManagerController->editPost((int) $post_id, $allcategories);
            }
            elseif($pages === 'delete/post'){
                $post_id = (string) ($_GET['post_id'] ?? '');
                $entriesRepository = $container->get('entriesRepository');
                $entriesRepository->clientDeletePost((int) $post_id);
                header("Location: index.php?" . http_build_query(['route' => 'client' , 'pages' => 'browse']));
            }
            elseif($pages === 'perma_delete/post'){
                $post_id = (string) ($_GET['post_id'] ?? '');
                $entriesRepository = $container->get('entriesRepository');
                $entriesRepository->permaDeletePost((int) $post_id);
                header("Location: index.php?" . http_build_query(['route' => 'client' , 'pages' => 'browse']));               
            }
            elseif($pages === 'deleted/posts'){
                $perPage = 10;
                $entriesManagerController = $container->get('entriesManagerController');
                $entriesManagerController->deletedPosts($perPage);
            }
            elseif($pages === 'reinstate/post'){
                $post_id = (string) ($_GET['post_id'] ?? '');
                $entriesRepository = $container->get('entriesRepository');
                $entriesRepository->reinstatePost((int) $post_id);
                header("Location: index.php?" . http_build_query(['route' => 'client' , 'pages' => 'post', 'post_id' => $post_id]));
            }
            elseif($pages === 'edit/users'){
                $usersManagerController = $container->get('usersManagerController');
                $usersManagerController->userList();
            }
            elseif($pages === 'edit/user'){
                $usersManagerController = $container->get('usersManagerController');
                $usersManagerController->editUser();
            }
            elseif($pages === 'delete/user'){
                $nickname = (string) ($_GET['nickname'] ?? '');
                $usersManagerController = $container->get('usersManagerController');
                $usersManagerController->deleteUser($nickname, $status, $isadmin);

            }
            elseif($pages === 'view/user'){
                $nickname = (string) ($_GET['nickname'] ?? '');
                header("Location: index.php?" . http_build_query(['route' => 'client' , 'pages' => 'profile', 'nickname' => $nickname ,'page' => 1]));
            }
            else {
                header("Location: index.php?" . http_build_query(['route' => 'client' , 'pages' => 'notFound']));
            }
        }
        else {
            $notFoundController = $container->get('notFoundController');
            $notFoundController->iseeyou();
        }
    }