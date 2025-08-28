<?php
if($pages === 'login'){
        require_once __DIR__ . '/src/Admin/Support/APILogin.php';
        exit;
    }
    elseif ($pages === 'create/post' && $_SERVER['REQUEST_METHOD'] === 'POST'){
        require_once __DIR__ . '/src/APIs/create.post.api.php';
        exit;
    }
    elseif ($pages === 'post'){
        $postID = (string) ($_GET['post_id'] ?? '');
        if ($postID === ''){
            $post = '';         
        }
        else {
            $entriesController = $container->get('entriesController');
            $post = $entriesController->postByIDAPI($postID);
        }
    }
    elseif ($pages === 'user/posts'){
        $nickname = (string) ($_GET['nickname'] ?? '');
        if ($nickname === ''){
            $nickname = '';         
        }
        else {
            $entriesController = $container->get('entriesController');
            $post = $entriesController->postsByNicknameAPI($nickname);
        }
    }
    elseif ($pages === 'user'){
        $nickname = (string) ($_GET['nickname'] ?? '');
        if ($nickname === ''){
            $nickname = '';         
        }
        else {
            $usersManagerController = $container->get('usersManagerController');
            $user = $usersManagerController->userByNicknameAPI($nickname);
        }
    }
    else {
        $notFoundController = $container->get('notFoundController');
        $notFoundController->error404();
    }