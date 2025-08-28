<?php

namespace App\FrontEnd\Controller;

use App\Repository\EntriesRepository;


class ClientController {

    public function render($view, $params) {
        extract($params);

        ob_start();
        require __DIR__ . '/../../../views/frontend/pages/' . $view . '.php';
        $contents = ob_get_clean();

        require __DIR__ . '/../../../views/frontend/layouts/main.view.php';
    }

    public function render_r ($view, $params){
        extract($params);

        ob_start();
        require __DIR__ . '/../../../views/frontend/pages/' . $view . '.php';
        $contents = ob_get_clean();

        require __DIR__ . '/../../../views/frontend/layouts/404_f.view.php';
    }

    public function showProfile($entries, $user, $isadmin, $isProfileOwner, $status, $page_num, $post_status){
        
        $page = (int)($_GET['page'] ?? 1);

        $this->render('profile', [
            'entries' => $entries,
            'user' => $user,
            'isadmin' => $isadmin,
            'isprofowner' => $isProfileOwner,
            'status' => $status,
            'num_pages'=> $page_num,
            'page'=> $page,
            'post_status' => $post_status
        ]);
    }

    public function showBlockedProfile($nickname, $status, $isadmin = false){

        $this->render('profile.blocked', [
            'user_nickname' => $nickname,
            'isadmin' => $isadmin,
            'status' => $status,
            'isprofowner' => false
        ]);
    }

    public function json($data, $code = 200){

        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

}